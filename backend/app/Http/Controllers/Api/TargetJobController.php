<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResumeVersion;
use App\Modules\ATS\Services\ATSAnalyzerService;
use App\Modules\Jobs\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TargetJobController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $jobs = Job::query()
            ->target()
            ->where(
                'created_by',
                $request->user()->id
            )
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $jobs,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateJob($request);

        $job = Job::query()->create([
            'created_by' =>
                $request->user()->id,

            'source_type' =>
                'target',

            'source_url' =>
                $validated['source_url'] ?? null,

            'title' =>
                $validated['title'],

            'company_name' =>
                $validated['company_name'] ?? null,

            'location' =>
                $validated['location'] ?? null,

            'employment_type' =>
                $validated['employment_type'] ?? null,

            'workplace_type' =>
                $validated['workplace_type'] ?? null,

            'description' =>
                $validated['description'],

            'requirements' =>
                $validated['requirements'] ?? null,

            'minimum_experience_years' =>
                $validated['minimum_experience_years']
                    ?? null,

            'education_level' =>
                $validated['education_level'] ?? null,

            'status' =>
                'active',

            'published_at' =>
                null,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Target job created successfully.',
            'data' => $job,
        ], 201);
    }

    public function show(
        Request $request,
        Job $job
    ): JsonResponse {
        $this->ensureOwnership(
            $request,
            $job
        );

        return response()->json([
            'success' => true,
            'data' => $job,
        ]);
    }

    public function update(
        Request $request,
        Job $job
    ): JsonResponse {
        $this->ensureOwnership(
            $request,
            $job
        );

        $validated = $this->validateJob(
            $request
        );

        $job->update([
            'source_url' =>
                $validated['source_url'] ?? null,

            'title' =>
                $validated['title'],

            'company_name' =>
                $validated['company_name'] ?? null,

            'location' =>
                $validated['location'] ?? null,

            'employment_type' =>
                $validated['employment_type'] ?? null,

            'workplace_type' =>
                $validated['workplace_type'] ?? null,

            'description' =>
                $validated['description'],

            'requirements' =>
                $validated['requirements'] ?? null,

            'minimum_experience_years' =>
                $validated['minimum_experience_years']
                    ?? null,

            'education_level' =>
                $validated['education_level'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Target job updated successfully.',
            'data' => $job->fresh(),
        ]);
    }

    public function destroy(
        Request $request,
        Job $job
    ): JsonResponse {
        $this->ensureOwnership(
            $request,
            $job
        );

        $job->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Target job deleted successfully.',
        ]);
    }

    public function analyze(
        Request $request,
        Job $job,
        ATSAnalyzerService $analyzer
    ): JsonResponse {
        $this->ensureOwnership(
            $request,
            $job
        );

        $validated = $request->validate([
            'resume_version_id' => [
                'required',
                'integer',
                'exists:resume_versions,id',
            ],
        ]);

        $resumeVersion = ResumeVersion::query()
            ->with('resume')
            ->findOrFail(
                $validated['resume_version_id']
            );

        abort_unless(
            $resumeVersion->resume
                && $resumeVersion->resume->user_id
                    === $request->user()->id,
            403
        );

        $analysis = $analyzer->analyze(
            $resumeVersion,
            $job,
            $request->user()
        );

        $analysis->load([
            'resumeVersion.resume',
            'job.skills',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Target job analysis completed successfully.',
            'data' => $analysis,
        ], 201);
    }

    private function ensureOwnership(
        Request $request,
        Job $job
    ): void {
        abort_unless(
            $job->source_type === 'target',
            404
        );

        abort_unless(
            $job->created_by
                === $request->user()->id,
            403
        );
    }

    private function validateJob(
        Request $request
    ): array {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:150',
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'location' => [
                'nullable',
                'string',
                'max:150',
            ],

            'source_url' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'employment_type' => [
                'nullable',
                'in:full_time,part_time,contract,internship,temporary',
            ],

            'workplace_type' => [
                'nullable',
                'in:onsite,remote,hybrid',
            ],

            'description' => [
                'required',
                'string',
            ],

            'requirements' => [
                'nullable',
                'string',
            ],

            'minimum_experience_years' => [
                'nullable',
                'integer',
                'min:0',
                'max:50',
            ],

            'education_level' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);
    }
}