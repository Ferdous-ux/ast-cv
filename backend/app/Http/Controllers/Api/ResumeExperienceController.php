<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeExperienceRequest;
use App\Http\Requests\UpdateResumeExperienceRequest;
use App\Models\Resume;
use App\Models\ResumeExperience;
use Illuminate\Http\JsonResponse;

class ResumeExperienceController extends Controller
{
    /**
     * Display a listing of experiences for a resume.
     */
    public function index(Resume $resume): JsonResponse
    {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $experiences = ResumeExperience::query()
            ->whereHas('resumeVersion', function ($query) use ($resume) {
                $query->where('resume_id', $resume->id);
            })
            ->orderBy('sort_order')
            ->orderByDesc('start_date')
            ->get();

        return response()->json([
            'data' => $experiences,
        ]);
    }

    /**
     * Store a newly created experience.
     */
    public function store(
        StoreResumeExperienceRequest $request,
        Resume $resume
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $validated = $request->validated();

        $version = $resume->versions()
            ->where('id', $validated['resume_version_id'])
            ->firstOrFail();

        $experience = $version->experiences()->create($validated);

        return response()->json([
            'message' => 'Experience created successfully.',
            'data' => $experience,
        ], 201);
    }

    /**
     * Display the specified experience.
     */
    public function show(
        Resume $resume,
        ResumeExperience $experience
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureExperienceBelongsToResume($resume, $experience);

        return response()->json([
            'data' => $experience,
        ]);
    }

    /**
     * Update the specified experience.
     */
    public function update(
        UpdateResumeExperienceRequest $request,
        Resume $resume,
        ResumeExperience $experience
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureExperienceBelongsToResume($resume, $experience);

        $experience->update($request->validated());

        return response()->json([
            'message' => 'Experience updated successfully.',
            'data' => $experience->fresh(),
        ]);
    }

    /**
     * Remove the specified experience.
     */
    public function destroy(
        Resume $resume,
        ResumeExperience $experience
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureExperienceBelongsToResume($resume, $experience);

        $experience->delete();

        return response()->json([
            'message' => 'Experience deleted successfully.',
        ]);
    }

    /**
     * Ensure the experience belongs to the given resume.
     */
    private function ensureExperienceBelongsToResume(
        Resume $resume,
        ResumeExperience $experience
    ): void {
        abort_unless(
            $experience->resumeVersion->resume_id === $resume->id,
            404
        );
    }
}