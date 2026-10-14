<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeLinkRequest;
use App\Http\Requests\UpdateResumeLinkRequest;
use App\Models\Resume;
use App\Models\ResumeLink;
use Illuminate\Http\JsonResponse;

class ResumeLinkController extends Controller
{
    public function index(Resume $resume): JsonResponse
    {
        $this->ensureResumeOwnership($resume);

        $links = ResumeLink::query()
            ->whereHas('resumeVersion', function ($query) use ($resume) {
                $query->where('resume_id', $resume->id);
            })
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $links,
        ]);
    }

    public function store(
        StoreResumeLinkRequest $request,
        Resume $resume
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $version = $resume->currentVersion;

        $validated = $request->validated();

        $link = ResumeLink::create([
            'resume_version_id' => $version->id,
            'label' => $validated['label'],
            'url' => $validated['url'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Link added successfully.',
            'data' => $link,
        ], 201);
    }

    public function show(
        Resume $resume,
        ResumeLink $link
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureLinkBelongsToResume(
            $resume,
            $link
        );

        return response()->json([
            'data' => $link,
        ]);
    }

    public function update(
        UpdateResumeLinkRequest $request,
        Resume $resume,
        ResumeLink $link
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureLinkBelongsToResume(
            $resume,
            $link
        );

        $link->update($request->validated());

        return response()->json([
            'message' => 'Link updated successfully.',
            'data' => $link->fresh(),
        ]);
    }

    public function destroy(
        Resume $resume,
        ResumeLink $link
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureLinkBelongsToResume(
            $resume,
            $link
        );

        $link->delete();

        return response()->json([
            'message' => 'Link removed successfully.',
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

    private function ensureLinkBelongsToResume(
        Resume $resume,
        ResumeLink $link
    ): void {
        abort_unless(
            $link->resumeVersion->resume_id === $resume->id,
            404
        );
    }
}