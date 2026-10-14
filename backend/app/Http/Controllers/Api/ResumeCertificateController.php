<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeCertificateRequest;
use App\Http\Requests\UpdateResumeCertificateRequest;
use App\Models\Resume;
use App\Models\ResumeCertificate;
use Illuminate\Http\JsonResponse;

class ResumeCertificateController extends Controller
{
    public function index(Resume $resume): JsonResponse
    {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $certificates = ResumeCertificate::query()
            ->whereHas('resumeVersion', function ($query) use ($resume) {
                $query->where('resume_id', $resume->id);
            })
            ->orderBy('sort_order')
            ->orderByDesc('issued_at')
            ->get();

        return response()->json([
            'data' => $certificates,
        ]);
    }

    public function store(
        StoreResumeCertificateRequest $request,
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

        $certificate = $version->certificates()->create($validated);

        return response()->json([
            'message' => 'Certificate created successfully.',
            'data' => $certificate,
        ], 201);
    }

    public function show(
        Resume $resume,
        ResumeCertificate $certificate
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureCertificateBelongsToResume(
            $resume,
            $certificate
        );

        return response()->json([
            'data' => $certificate,
        ]);
    }

    public function update(
        UpdateResumeCertificateRequest $request,
        Resume $resume,
        ResumeCertificate $certificate
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureCertificateBelongsToResume(
            $resume,
            $certificate
        );

        $certificate->update($request->validated());

        return response()->json([
            'message' => 'Certificate updated successfully.',
            'data' => $certificate->fresh(),
        ]);
    }

    public function destroy(
        Resume $resume,
        ResumeCertificate $certificate
    ): JsonResponse {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );

        $this->ensureCertificateBelongsToResume(
            $resume,
            $certificate
        );

        $certificate->delete();

        return response()->json([
            'message' => 'Certificate deleted successfully.',
        ]);
    }

    private function ensureCertificateBelongsToResume(
        Resume $resume,
        ResumeCertificate $certificate
    ): void {
        abort_unless(
            $certificate->resumeVersion->resume_id === $resume->id,
            404
        );
    }
}