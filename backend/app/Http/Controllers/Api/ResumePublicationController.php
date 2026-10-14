<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumePublicationRequest;
use App\Http\Requests\UpdateResumePublicationRequest;
use App\Models\Resume;
use App\Models\ResumePublication;
use Illuminate\Http\JsonResponse;

class ResumePublicationController extends Controller
{
    public function index(Resume $resume): JsonResponse
    {
        $this->ensureResumeOwnership($resume);

        $publications = ResumePublication::query()
            ->whereHas('resumeVersion', function ($query) use ($resume) {
                $query->where('resume_id', $resume->id);
            })
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $publications,
        ]);
    }

    public function store(
        StoreResumePublicationRequest $request,
        Resume $resume
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $version = $resume->currentVersion;

        $validated = $request->validated();

        $publication = ResumePublication::create([
            'resume_version_id' => $version->id,
            'title' => $validated['title'],
            'publisher' => $validated['publisher'] ?? null,
            'description' => $validated['description'] ?? null,
            'published_at' => $validated['published_at'] ?? null,
            'url' => $validated['url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Publication added successfully.',
            'data' => $publication,
        ], 201);
    }

    public function show(
        Resume $resume,
        ResumePublication $publication
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensurePublicationBelongsToResume(
            $resume,
            $publication
        );

        return response()->json([
            'data' => $publication,
        ]);
    }

    public function update(
        UpdateResumePublicationRequest $request,
        Resume $resume,
        ResumePublication $publication
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensurePublicationBelongsToResume(
            $resume,
            $publication
        );

        $publication->update($request->validated());

        return response()->json([
            'message' => 'Publication updated successfully.',
            'data' => $publication->fresh(),
        ]);
    }

    public function destroy(
        Resume $resume,
        ResumePublication $publication
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensurePublicationBelongsToResume(
            $resume,
            $publication
        );

        $publication->delete();

        return response()->json([
            'message' => 'Publication removed successfully.',
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

    private function ensurePublicationBelongsToResume(
        Resume $resume,
        ResumePublication $publication
    ): void {
        abort_unless(
            $publication->resumeVersion->resume_id === $resume->id,
            404
        );
    }
}