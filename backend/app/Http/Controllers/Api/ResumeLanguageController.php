<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeLanguageRequest;
use App\Http\Requests\UpdateResumeLanguageRequest;
use App\Models\Resume;
use App\Models\ResumeLanguage;
use Illuminate\Http\JsonResponse;

class ResumeLanguageController extends Controller
{
    public function index(Resume $resume): JsonResponse
    {
        $this->ensureResumeOwnership($resume);

        $languages = ResumeLanguage::query()
            ->with('language')
            ->whereHas('resumeVersion', function ($query) use ($resume) {
                $query->where('resume_id', $resume->id);
            })
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $languages,
        ]);
    }

    public function store(
        StoreResumeLanguageRequest $request,
        Resume $resume
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $version = $resume->currentVersion;

        $validated = $request->validated();

        $resumeLanguage = ResumeLanguage::create([
            'resume_version_id' => $version->id,
            'language_id' => $validated['language_id'],
            'proficiency' => $validated['proficiency'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Language added successfully.',
            'data' => $resumeLanguage->load('language'),
        ], 201);
    }

    public function show(
        Resume $resume,
        ResumeLanguage $language
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureLanguageBelongsToResume(
            $resume,
            $language
        );

        return response()->json([
            'data' => $language->load('language'),
        ]);
    }

    public function update(
        UpdateResumeLanguageRequest $request,
        Resume $resume,
        ResumeLanguage $language
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureLanguageBelongsToResume(
            $resume,
            $language
        );

        $language->update($request->validated());

        return response()->json([
            'message' => 'Language updated successfully.',
            'data' => $language->fresh()->load('language'),
        ]);
    }

    public function destroy(
        Resume $resume,
        ResumeLanguage $language
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureLanguageBelongsToResume(
            $resume,
            $language
        );

        $language->delete();

        return response()->json([
            'message' => 'Language removed successfully.',
        ]);
    }

    private function ensureResumeOwnership(
        Resume $resume
    ): void {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );
    }

    private function ensureLanguageBelongsToResume(
        Resume $resume,
        ResumeLanguage $language
    ): void {
        abort_unless(
            $language->resumeVersion->resume_id === $resume->id,
            404
        );
    }
}