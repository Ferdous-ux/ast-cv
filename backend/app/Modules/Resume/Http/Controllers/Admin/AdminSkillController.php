<?php

namespace App\Modules\Resume\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\SkillAlias;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminSkillController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim(
            (string) $request->get('search')
        );

        $category = trim(
            (string) $request->get('category')
        );

        $skills = Skill::query()
            ->select('skills.*')
            ->selectSub(
                function ($query) {
                    $query
                        ->from('job_skills')
                        ->selectRaw('COUNT(*)')
                        ->whereColumn(
                            'job_skills.skill_id',
                            'skills.id'
                        );
                },
                'jobs_count'
            )
            ->withCount([
                'aliases',
                'resumeVersions',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'category',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'aliases',
                                    function ($query) use ($search) {
                                        $query->where(
                                            'alias',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                );
                        }
                    );
                }
            )
            ->when(
                $category !== '',
                fn ($query) => $query->where(
                    'category',
                    $category
                )
            )
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = Skill::query()
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $totalSkills = Skill::count();

        $totalAliases = SkillAlias::count();

        $categoriesCount = Skill::query()
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->distinct()
            ->count('category');

        $usedSkills = Skill::query()
            ->where(
                function ($query) {
                    $query
                        ->whereHas('resumeVersions')
                        ->orWhereIn(
                            'id',
                            DB::table('job_skills')
                                ->select('skill_id')
                        );
                }
            )
            ->count();

        return view(
            'admin.skills.index',
            compact(
                'skills',
                'categories',
                'search',
                'category',
                'totalSkills',
                'totalAliases',
                'categoriesCount',
                'usedSkills'
            )
        );
    }

    public function create(): View
    {
        $aliasesText = '';

        return view(
            'admin.skills.create',
            compact('aliasesText')
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $data = $this->validatedData($request);

        $aliases = $this->parseAliases(
            $data['aliases_text'] ?? ''
        );

        $this->ensureAliasesAvailable(
            $aliases
        );

        unset($data['aliases_text']);

        DB::transaction(
            function () use ($data, $aliases) {
                $skill = Skill::create($data);

                $this->syncAliases(
                    $skill,
                    $aliases
                );
            }
        );

        return redirect()
            ->route('admin.skills.index')
            ->with(
                'success',
                __('admin_skills.create_success')
            );
    }

    public function edit(
        Skill $skill
    ): View {
        $skill->load('aliases');

        $aliasesText = $skill
            ->aliases
            ->map(
                function ($alias) {
                    $value = $alias->alias;

                    if ($alias->language) {
                        $value .= ' | ' . $alias->language;
                    }

                    return $value;
                }
            )
            ->implode(PHP_EOL);

        return view(
            'admin.skills.edit',
            compact(
                'skill',
                'aliasesText'
            )
        );
    }

    public function update(
        Request $request,
        Skill $skill
    ): RedirectResponse {
        $data = $this->validatedData(
            $request,
            $skill
        );

        $aliases = $this->parseAliases(
            $data['aliases_text'] ?? ''
        );

        $this->ensureAliasesAvailable(
            $aliases,
            $skill
        );

        unset($data['aliases_text']);

        DB::transaction(
            function () use (
                $skill,
                $data,
                $aliases
            ) {
                $skill->update($data);

                $this->syncAliases(
                    $skill,
                    $aliases
                );
            }
        );

        return redirect()
            ->route('admin.skills.index')
            ->with(
                'success',
                __('admin_skills.update_success')
            );
    }

    public function destroy(
        Skill $skill
    ): RedirectResponse {
        $usedInResume = DB::table(
            'resume_skills'
        )
            ->where(
                'skill_id',
                $skill->id
            )
            ->exists();

        $usedInJob = DB::table(
            'job_skills'
        )
            ->where(
                'skill_id',
                $skill->id
            )
            ->exists();

        if ($usedInResume || $usedInJob) {
            return redirect()
                ->route('admin.skills.index')
                ->with(
                    'error',
                    __('admin_skills.delete_in_use')
                );
        }

        DB::transaction(
            function () use ($skill) {
                $skill->aliases()->delete();

                $skill->delete();
            }
        );

        return redirect()
            ->route('admin.skills.index')
            ->with(
                'success',
                __('admin_skills.delete_success')
            );
    }

    private function validatedData(
        Request $request,
        ?Skill $skill = null
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique(
                    'skills',
                    'name'
                )->ignore($skill?->id),
            ],

            'category' => [
                'nullable',
                'string',
                'max:50',
            ],

            'aliases_text' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ]);
    }

    private function parseAliases(
        string $value
    ): array {
        $lines = preg_split(
            '/\r\n|\r|\n/u',
            $value
        );

        $aliases = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $parts = explode(
                '|',
                $line,
                2
            );

            $alias = trim(
                $parts[0]
            );

            $language = isset($parts[1])
                ? strtolower(trim($parts[1]))
                : null;

            if (
                ! in_array(
                    $language,
                    ['ar', 'en'],
                    true
                )
            ) {
                $language = null;
            }

            $normalized = $this->normalizeAlias(
                $alias
            );

            if ($normalized === '') {
                continue;
            }

            $key = $normalized
                . '|'
                . ($language ?? '');

            $aliases[$key] = [
                'alias' => $alias,
                'normalized_alias' => $normalized,
                'language' => $language,
            ];
        }

        return array_values($aliases);
    }

    private function normalizeAlias(
        string $value
    ): string {
        $value = trim($value);

        $value = preg_replace(
            '/\s+/u',
            ' ',
            $value
        );

        return mb_strtolower(
            $value
        );
    }

    private function ensureAliasesAvailable(
        array $aliases,
        ?Skill $skill = null
    ): void {
        foreach ($aliases as $alias) {
            $exists = SkillAlias::query()
                ->where(
                    'normalized_alias',
                    $alias['normalized_alias']
                )
                ->when(
                    $skill,
                    fn ($query) => $query->where(
                        'skill_id',
                        '<>',
                        $skill->id
                    )
                )
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'aliases_text' => __(
                        'admin_skills.alias_conflict',
                        [
                            'alias' => $alias['alias'],
                        ]
                    ),
                ]);
            }
        }
    }

    private function syncAliases(
        Skill $skill,
        array $aliases
    ): void {
        $skill->aliases()->delete();

        foreach ($aliases as $alias) {
            SkillAlias::create([
                'skill_id' => $skill->id,
                'alias' => $alias['alias'],
                'normalized_alias' => $alias['normalized_alias'],
                'language' => $alias['language'],
            ]);
        }
    }
}