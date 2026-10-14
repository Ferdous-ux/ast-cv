<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeEducationRequest;
use App\Http\Requests\UpdateResumeEducationRequest;
use App\Models\Resume;
use App\Models\ResumeEducation;
use Illuminate\Http\JsonResponse;

class ResumeEducationController extends Controller
{
    /**
     * Display a listing of educations for a resume.
     */
    public function index(Resume $resume): JsonResponse
    {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $educations = ResumeEducation::query()
            ->whereHas('resumeVersion', function ($query) use ($resume) {
                $query->where('resume_id', $resume->id);
            })
            ->orderBy('sort_order')
            ->orderByDesc('start_date')
            ->get();

        return response()->json([
            'data' => $educations,
        ]);
    }

    /**
     * Store a newly created education.
     */
    public function store(
        StoreResumeEducationRequest $request,
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

        $education = $version->educations()->create($validated);

        return response()->json([
            'message' => 'Education created successfully.',
            'data' => $education,
        ], 201);
    }

    /**
     * Display the specified education.
     */
    public function show(
        Resume $resume,
        ResumeEducation $education
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureEducationBelongsToResume($resume, $education);

        return response()->json([
            'data' => $education,
        ]);
    }

    /**
     * Update the specified education.
     */
    public function update(
        UpdateResumeEducationRequest $request,
        Resume $resume,
        ResumeEducation $education
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureEducationBelongsToResume($resume, $education);

        $education->update($request->validated());

        return response()->json([
            'message' => 'Education updated successfully.',
            'data' => $education->fresh(),
        ]);
    }

    /**
     * Remove the specified education.
     */
    public function destroy(
        Resume $resume,
        ResumeEducation $education
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureEducationBelongsToResume($resume, $education);

        $education->delete();

        return response()->json([
            'message' => 'Education deleted successfully.',
        ]);
    }

    /**
     * Ensure the education belongs to the given resume.
     */
    private function ensureEducationBelongsToResume(
        Resume $resume,
        ResumeEducation $education
    ): void {
        abort_unless(
            $education->resumeVersion->resume_id === $resume->id,
            404
        );
    }
}