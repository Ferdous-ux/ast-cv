<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomResumeSkillRequest;
use App\Http\Requests\StoreResumeSkillRequest;
use App\Http\Requests\StoreResumeSkillsBulkRequest;
use App\Http\Requests\UpdateResumeSkillRequest;
use App\Models\Resume;
use App\Models\ResumeSkill;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResumeSkillController extends Controller
{
    public function index(Resume $resume): JsonResponse
    {
        $this->ensureResumeOwnership($resume);

        $skills = ResumeSkill::query()
            ->with('skill')
            ->whereHas(
                'resumeVersion',
                function ($query) use ($resume) {
                    $query->where(
                        'resume_id',
                        $resume->id
                    );
                }
            )
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $skills,
        ]);
    }

    public function store(
        StoreResumeSkillRequest $request,
        Resume $resume
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $version = $this->getCurrentVersion(
            $resume
        );

        $validated = $request->validated();

        $this->ensureSkillNotAlreadyAdded(
            $version->id,
            (int) $validated['skill_id']
        );

        $resumeSkill = ResumeSkill::create([
            'resume_version_id' => $version->id,
            'skill_id' => $validated['skill_id'],
            'level' => $validated['level'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Skill added successfully.',
            'data' => $resumeSkill->load('skill'),
        ], 201);
    }

    public function storeBulk(
        StoreResumeSkillsBulkRequest $request,
        Resume $resume
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $version = $this->getCurrentVersion(
            $resume
        );

        $validated = $request->validated();

        $result = DB::transaction(
            function () use (
                $validated,
                $version
            ) {
                $added = [];
                $skipped = [];

                foreach (
                    $validated['skills']
                    as $index => $item
                ) {
                    $skillId = (int) $item['skill_id'];

                    $alreadyExists = ResumeSkill::query()
                        ->where(
                            'resume_version_id',
                            $version->id
                        )
                        ->where(
                            'skill_id',
                            $skillId
                        )
                        ->exists();

                    if ($alreadyExists) {
                        $skipped[] = $skillId;
                        continue;
                    }

                    $resumeSkill = ResumeSkill::create([
                        'resume_version_id' => $version->id,
                        'skill_id' => $skillId,
                        'level' => $item['level'] ?? null,
                        'sort_order' =>
                            $item['sort_order'] ?? $index,
                    ]);

                    $added[] = $resumeSkill;
                }

                return [
                    'added' => $added,
                    'skipped' => $skipped,
                ];
            }
        );

        $addedIds = collect(
            $result['added']
        )->pluck('id');

        $added = ResumeSkill::query()
            ->with('skill')
            ->whereIn('id', $addedIds)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'message' => 'Skills processed successfully.',
            'data' => $added,
            'meta' => [
                'added_count' => $added->count(),
                'skipped_count' => count(
                    $result['skipped']
                ),
                'skipped_skill_ids' =>
                    $result['skipped'],
            ],
        ], 201);
    }

    public function storeCustom(
        StoreCustomResumeSkillRequest $request,
        Resume $resume
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $version = $this->getCurrentVersion(
            $resume
        );

        $validated = $request->validated();

        $name = trim(
            preg_replace(
                '/\s+/u',
                ' ',
                $validated['name']
            )
        );

        $normalizedName = mb_strtolower(
            $name
        );

        $result = DB::transaction(
            function () use (
                $validated,
                $name,
                $normalizedName,
                $version
            ) {
                $skill = Skill::query()
                    ->whereRaw(
                        'LOWER(name) = ?',
                        [$normalizedName]
                    )
                    ->first();

                $skillCreated = false;

                if (!$skill) {
                    $skill = Skill::create([
                        'name' => $name,
                        'category' =>
                            $validated['category'] ?? null,
                        'origin' => Skill::ORIGIN_USER,
                        'approval_status' =>
                            Skill::STATUS_PENDING,
                        'created_by' => auth()->id(),
                    ]);

                    $skillCreated = true;
                }

                $alreadyExists = ResumeSkill::query()
                    ->where(
                        'resume_version_id',
                        $version->id
                    )
                    ->where(
                        'skill_id',
                        $skill->id
                    )
                    ->exists();

                if ($alreadyExists) {
                    throw ValidationException::withMessages([
                        'name' => [
                            'This skill has already been added to the current resume version.',
                        ],
                    ]);
                }

                $resumeSkill = ResumeSkill::create([
                    'resume_version_id' => $version->id,
                    'skill_id' => $skill->id,
                    'level' => $validated['level'] ?? null,
                    'sort_order' =>
                        $validated['sort_order'] ?? 0,
                ]);

                return [
                    'resume_skill' => $resumeSkill,
                    'skill_created' => $skillCreated,
                ];
            }
        );

        $resumeSkill = $result['resume_skill']
            ->load('skill');

        return response()->json([
            'message' => $result['skill_created']
                ? 'Custom skill created and added successfully.'
                : 'Existing skill added successfully.',
            'data' => $resumeSkill,
            'meta' => [
                'custom_skill_created' =>
                    $result['skill_created'],
            ],
        ], 201);
    }

    public function show(
        Resume $resume,
        ResumeSkill $skill
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureSkillBelongsToResume(
            $resume,
            $skill
        );

        return response()->json([
            'data' => $skill->load('skill'),
        ]);
    }

    public function update(
        UpdateResumeSkillRequest $request,
        Resume $resume,
        ResumeSkill $skill
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureSkillBelongsToResume(
            $resume,
            $skill
        );

        $skill->update(
            $request->validated()
        );

        return response()->json([
            'message' => 'Skill updated successfully.',
            'data' => $skill
                ->fresh()
                ->load('skill'),
        ]);
    }

    public function destroy(
        Resume $resume,
        ResumeSkill $skill
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureSkillBelongsToResume(
            $resume,
            $skill
        );

        $skill->delete();

        return response()->json([
            'message' => 'Skill removed successfully.',
        ]);
    }

    private function getCurrentVersion(
        Resume $resume
    ) {
        $version = $resume->currentVersion;

        if (!$version) {
            throw ValidationException::withMessages([
                'resume' => [
                    'Resume does not have a current version.',
                ],
            ]);
        }

        return $version;
    }

    private function ensureSkillNotAlreadyAdded(
        int $versionId,
        int $skillId
    ): void {
        $exists = ResumeSkill::query()
            ->where(
                'resume_version_id',
                $versionId
            )
            ->where(
                'skill_id',
                $skillId
            )
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'skill_id' => [
                    'This skill has already been added to the current resume version.',
                ],
            ]);
        }
    }

    private function ensureResumeOwnership(
        Resume $resume
    ): void {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );
    }

    private function ensureSkillBelongsToResume(
        Resume $resume,
        ResumeSkill $skill
    ): void {
        abort_unless(
            $skill->resumeVersion->resume_id === $resume->id,
            404
        );
    }
}