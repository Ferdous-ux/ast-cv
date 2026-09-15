<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeAwardRequest;
use App\Http\Requests\UpdateResumeAwardRequest;
use App\Models\Resume;
use App\Models\ResumeAward;
use Illuminate\Http\JsonResponse;

class ResumeAwardController extends Controller
{
    public function index(Resume $resume): JsonResponse
    {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $awards = ResumeAward::query()
            ->whereHas('resumeVersion', function ($query) use ($resume) {
                $query->where('resume_id', $resume->id);
            })
            ->orderBy('sort_order')
            ->orderByDesc('awarded_at')
            ->get();

        return response()->json([
            'data' => $awards,
        ]);
    }

    public function store(
        StoreResumeAwardRequest $request,
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

        $award = $version->awards()->create($validated);

        return response()->json([
            'message' => 'Award created successfully.',
            'data' => $award,
        ], 201);
    }

    public function show(
        Resume $resume,
        ResumeAward $award
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureAwardBelongsToResume(
            $resume,
            $award
        );

        return response()->json([
            'data' => $award,
        ]);
    }

    public function update(
        UpdateResumeAwardRequest $request,
        Resume $resume,
        ResumeAward $award
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureAwardBelongsToResume(
            $resume,
            $award
        );

        $award->update($request->validated());

        return response()->json([
            'message' => 'Award updated successfully.',
            'data' => $award->fresh(),
        ]);
    }

    public function destroy(
        Resume $resume,
        ResumeAward $award
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureAwardBelongsToResume(
            $resume,
            $award
        );

        $award->delete();

        return response()->json([
            'message' => 'Award deleted successfully.',
        ]);
    }

    private function ensureAwardBelongsToResume(
        Resume $resume,
        ResumeAward $award
    ): void {
        abort_unless(
            $award->resumeVersion->resume_id === $resume->id,
            404
        );
    }
}