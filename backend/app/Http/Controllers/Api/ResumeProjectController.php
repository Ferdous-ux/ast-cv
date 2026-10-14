<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeProjectRequest;
use App\Http\Requests\UpdateResumeProjectRequest;
use App\Models\Resume;
use App\Models\ResumeProject;
use Illuminate\Http\JsonResponse;

class ResumeProjectController extends Controller
{
    public function index(Resume $resume): JsonResponse
    {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $projects = ResumeProject::query()
            ->whereHas('resumeVersion', function ($query) use ($resume) {
                $query->where('resume_id', $resume->id);
            })
            ->orderBy('sort_order')
            ->orderByDesc('start_date')
            ->get();

        return response()->json([
            'data' => $projects,
        ]);
    }

    public function store(
        StoreResumeProjectRequest $request,
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

        $project = $version->projects()->create($validated);

        return response()->json([
            'message' => 'Project created successfully.',
            'data' => $project,
        ], 201);
    }

    public function show(
        Resume $resume,
        ResumeProject $project
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureProjectBelongsToResume(
            $resume,
            $project
        );

        return response()->json([
            'data' => $project,
        ]);
    }

    public function update(
        UpdateResumeProjectRequest $request,
        Resume $resume,
        ResumeProject $project
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureProjectBelongsToResume(
            $resume,
            $project
        );

        $project->update($request->validated());

        return response()->json([
            'message' => 'Project updated successfully.',
            'data' => $project->fresh(),
        ]);
    }

    public function destroy(
        Resume $resume,
        ResumeProject $project
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureProjectBelongsToResume(
            $resume,
            $project
        );

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.',
        ]);
    }

    private function ensureProjectBelongsToResume(
        Resume $resume,
        ResumeProject $project
    ): void {
        abort_unless(
            $project->resumeVersion->resume_id === $resume->id,
            404
        );
    }
}