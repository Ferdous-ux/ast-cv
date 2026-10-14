<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeVersionRequest;
use App\Http\Requests\UpdateResumeVersionRequest;
use App\Models\Resume;
use App\Models\ResumeVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ResumeVersionController extends Controller
{
    /**
     * List all versions of a resume.
     */
    public function index(Resume $resume): JsonResponse
    {
        $this->ensureResumeOwnership($resume);

        $versions = $resume->versions()
            ->orderByDesc('version_number')
            ->get();

        return response()->json([
            'data' => $versions,
        ]);
    }

    /**
     * Create a new version.
     */
    public function store(
        StoreResumeVersionRequest $request,
        Resume $resume
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $validated = $request->validated();

        $version = DB::transaction(function () use (
            $resume,
            $validated
        ) {
            $lastVersionNumber = $resume->versions()
                ->max('version_number') ?? 0;

            // Archive all previous versions.
            $resume->versions()->update([
                'status' => 'archived',
            ]);

            // Create the new active version.
            $version = $resume->versions()->create([
                'version_number' => $lastVersionNumber + 1,
                'status' => 'active',
                'summary' => $validated['summary'] ?? null,
                'template' => $validated['template'] ?? 'classic',
            ]);

            // Set the new version as the current version.
            $resume->update([
                'current_version_id' => $version->id,
            ]);

            return $version;
        });

        return response()->json([
            'message' => 'Resume version created successfully.',
            'data' => $version,
        ], 201);
    }

    /**
     * Show a specific version.
     */
    public function show(
        Resume $resume,
        ResumeVersion $version
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);
        $this->ensureVersionBelongsToResume($resume, $version);

        return response()->json([
            'data' => $version,
        ]);
    }

    /**
     * Update a version.
     */
    public function update(
        UpdateResumeVersionRequest $request,
        Resume $resume,
        ResumeVersion $version
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);
        $this->ensureVersionBelongsToResume($resume, $version);

        $version->update($request->validated());

        return response()->json([
            'message' => 'Resume version updated successfully.',
            'data' => $version->fresh(),
        ]);
    }

    /**
     * Activate a version.
     */
    public function activate(
        Resume $resume,
        ResumeVersion $version
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);
        $this->ensureVersionBelongsToResume($resume, $version);

        DB::transaction(function () use ($resume, $version) {
            // Archive all other versions.
            $resume->versions()
                ->where('id', '!=', $version->id)
                ->update([
                    'status' => 'archived',
                ]);

            // Activate the selected version.
            $version->update([
                'status' => 'active',
            ]);

            // Make it the current version.
            $resume->update([
                'current_version_id' => $version->id,
            ]);
        });

        return response()->json([
            'message' => 'Resume version activated successfully.',
            'data' => $version->fresh(),
        ]);
    }

    /**
     * Delete a version.
     */
    public function destroy(
        Resume $resume,
        ResumeVersion $version
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);
        $this->ensureVersionBelongsToResume($resume, $version);

        if ($resume->versions()->count() <= 1) {
            return response()->json([
                'message' => 'A resume must have at least one version.',
            ], 422);
        }

        DB::transaction(function () use ($resume, $version) {
            $wasCurrent = $resume->current_version_id === $version->id;

            $version->delete();

            if ($wasCurrent) {
                $newCurrentVersion = $resume->versions()
                    ->orderByDesc('version_number')
                    ->first();

                $newCurrentVersion->update([
                    'status' => 'active',
                ]);

                $resume->update([
                    'current_version_id' => $newCurrentVersion->id,
                ]);
            }
        });

        return response()->json([
            'message' => 'Resume version deleted successfully.',
        ]);
    }

    /**
     * Ensure the authenticated user owns the resume.
     */
    private function ensureResumeOwnership(
        Resume $resume
    ): void {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );
    }

    /**
     * Ensure the version belongs to the given resume.
     */
    private function ensureVersionBelongsToResume(
        Resume $resume,
        ResumeVersion $version
    ): void {
        abort_unless(
            $version->resume_id === $resume->id,
            404
        );
    }
}