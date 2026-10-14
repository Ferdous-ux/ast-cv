<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResumeVersion;
use App\Modules\ATS\Models\ATSAnalysis;
use App\Modules\ATS\Services\ATSAnalyzerService;
use App\Modules\Jobs\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ATSController extends Controller
{
    public function index(
        Request $request
    ): JsonResponse {
        $validated =
            $request->validate([
                'resume_version_id' => [
                    'nullable',
                    'integer',
                    'exists:resume_versions,id',
                ],

                'job_id' => [
                    'nullable',
                    'integer',
                    'exists:job_postings,id',
                ],
            ]);

        $analyses =
            ATSAnalysis::query()
                ->where(
                    'analyzed_by',
                    $request->user()->id
                )
                ->with([
                    'resumeVersion.resume',
                    'job',
                ])
                ->when(
                    isset(
                        $validated[
                            'resume_version_id'
                        ]
                    ),
                    fn ($query) =>
                        $query->where(
                            'resume_version_id',
                            $validated[
                                'resume_version_id'
                            ]
                        )
                )
                ->when(
                    isset(
                        $validated['job_id']
                    ),
                    fn ($query) =>
                        $query->where(
                            'job_posting_id',
                            $validated['job_id']
                        )
                )
                ->orderByDesc(
                    'analyzed_at'
                )
                ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $analyses,
        ]);
    }

    public function analyze(
        Request $request,
        ATSAnalyzerService $analyzer
    ): JsonResponse {
        $validated =
            $request->validate([
                'resume_version_id' => [
                    'required',
                    'integer',
                    'exists:resume_versions,id',
                ],

                'job_id' => [
                    'required',
                    'integer',

                    Rule::exists(
                        'job_postings',
                        'id'
                    )->where(
                        fn ($query) =>
                            $query
                                ->where(
                                    'source_type',
                                    'platform'
                                )
                                ->where(
                                    'status',
                                    'active'
                                )
                    ),
                ],
            ]);

        $resumeVersion =
            ResumeVersion::query()
                ->with('resume')
                ->findOrFail(
                    $validated[
                        'resume_version_id'
                    ]
                );

        abort_unless(
            $resumeVersion->resume
                && $resumeVersion
                    ->resume
                    ->user_id
                    === $request
                        ->user()
                        ->id,
            403
        );

        $job = Job::query()
            ->active()
            ->with('skills')
            ->findOrFail(
                $validated['job_id']
            );

        $analysis =
            $analyzer->analyze(
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
                'ATS analysis completed successfully.',

            'data' => $analysis,
        ], 201);
    }

    public function show(
        Request $request,
        ATSAnalysis $analysis
    ): JsonResponse {
        abort_unless(
            $analysis->analyzed_by
                === $request->user()->id,
            403
        );

        $analysis->load([
            'resumeVersion.resume',
            'job.skills',
        ]);

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }
}