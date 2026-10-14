<?php


namespace App\Modules\Resume\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminLanguageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));
        $status = trim((string) $request->get('status'));

        $languages = Language::query()
            ->withCount([
                'resumes',
                'resumeVersions',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('native_name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when(
                $status === 'active',
                fn ($query) => $query->where('is_active', true)
            )
            ->when(
                $status === 'inactive',
                fn ($query) => $query->where('is_active', false)
            )
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $totalLanguages = Language::count();

        $activeLanguages = Language::query()
            ->where('is_active', true)
            ->count();

        $inactiveLanguages = Language::query()
            ->where('is_active', false)
            ->count();

        $usedLanguages = Language::query()
            ->where(function ($query) {
                $query
                    ->whereHas('resumes')
                    ->orWhereHas('resumeVersions');
            })
            ->count();

        return view(
            'admin.languages.index',
            compact(
                'languages',
                'search',
                'status',
                'totalLanguages',
                'activeLanguages',
                'inactiveLanguages',
                'usedLanguages'
            )
        );
    }

    public function create(): View
    {
        return view('admin.languages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['code'] = strtolower(trim($data['code']));
        $data['name'] = trim($data['name']);

        $data['native_name'] = filled($data['native_name'] ?? null)
            ? trim($data['native_name'])
            : null;

        Language::create($data);

        return redirect()
            ->route('admin.languages.index')
            ->with(
                'success',
                __('admin_languages.create_success')
            );
    }

    public function edit(Language $language): View
    {
        $language->loadCount([
            'resumes',
            'resumeVersions',
        ]);

        return view(
            'admin.languages.edit',
            compact('language')
        );
    }

    public function update(
        Request $request,
        Language $language
    ): RedirectResponse {
        $data = $this->validatedData(
            $request,
            $language
        );

        $data['code'] = strtolower(trim($data['code']));
        $data['name'] = trim($data['name']);

        $data['native_name'] = filled($data['native_name'] ?? null)
            ? trim($data['native_name'])
            : null;

        $language->update($data);

        return redirect()
            ->route('admin.languages.index')
            ->with(
                'success',
                __('admin_languages.update_success')
            );
    }

    public function destroy(Language $language): RedirectResponse
    {
        $usedAsWritingLanguage = DB::table('resumes')
            ->where(
                'language_id',
                $language->id
            )
            ->exists();

        $usedAsSpokenLanguage = DB::table('resume_languages')
            ->where(
                'language_id',
                $language->id
            )
            ->exists();

        if ($usedAsWritingLanguage || $usedAsSpokenLanguage) {
            return redirect()
                ->route('admin.languages.index')
                ->with(
                    'error',
                    __('admin_languages.delete_in_use')
                );
        }

        $language->delete();

        return redirect()
            ->route('admin.languages.index')
            ->with(
                'success',
                __('admin_languages.delete_success')
            );
    }

    private function validatedData(
        Request $request,
        ?Language $language = null
    ): array {
        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('languages', 'code')
                    ->ignore($language?->id),
            ],

            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('languages', 'name')
                    ->ignore($language?->id),
            ],

            'native_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'direction' => [
                'required',
                Rule::in([
                    'ltr',
                    'rtl',
                ]),
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);
    }
}
