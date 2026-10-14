<?php

namespace App\Modules\Matching\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Modules\Jobs\Models\Job;
use App\Modules\Matching\Services\MatchingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MatchingController extends Controller
{
    public function index()
    {
        $currentVersionIds = Resume::query()
            ->whereNotNull('current_version_id')
            ->pluck('current_version_id')
            ->filter()
            ->unique()
            ->values();

        $resumeVersions = ResumeVersion::query()
            ->whereIn('id', $currentVersionIds)
            ->with([
                'resume.user',
            ])
            ->latest('id')
            ->get();

        $jobs = Job::query()
            ->active()
            ->with('skills')
            ->latest()
            ->get();

        $totalCurrentResumes =
            $resumeVersions->count();

        $totalActiveJobs =
            $jobs->count();

        return view(
            'admin.matching.index',
            compact(
                'resumeVersions',
                'jobs',
                'totalCurrentResumes',
                'totalActiveJobs'
            )
        );
    }

    public function run(
        Request $request,
        MatchingService $matchingService
    ) {
        $data = $request->validate([
            'mode' => [
                'required',
                Rule::in([
                    'resume_to_jobs',
                    'job_to_resumes',
                ]),
            ],

            'resume_version_id' => [
                Rule::requiredIf(
                    fn () =>
                        $request->input('mode')
                        === 'resume_to_jobs'
                ),
                'nullable',
                'integer',
                'exists:resume_versions,id',
            ],

            'job_id' => [
                Rule::requiredIf(
                    fn () =>
                        $request->input('mode')
                        === 'job_to_resumes'
                ),
                'nullable',
                'integer',
                Rule::exists(
                    'job_postings',
                    'id'
                )->where(
                    fn ($query) =>
                        $query->where(
                            'status',
                            'active'
                        )
                ),
            ],

            'limit' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $limit = (int) $data['limit'];

        if (
            $data['mode']
            === 'resume_to_jobs'
        ) {
            $resumeVersion =
                ResumeVersion::query()
                    ->with([
                        'resume.user',
                    ])
                    ->findOrFail(
                        $data[
                            'resume_version_id'
                        ]
                    );

            $results =
                $matchingService
                    ->rankJobsForResume(
                        $resumeVersion,
                        auth()->user(),
                        $limit
                    );

            $job = null;

            return view(
                'admin.matching.results',
                compact(
                    'results',
                    'resumeVersion',
                    'job',
                    'limit'
                ) + [
                    'mode' =>
                        'resume_to_jobs',
                ]
            );
        }

        $job = Job::query()
            ->active()
            ->with('skills')
            ->findOrFail(
                $data['job_id']
            );

        $results =
            $matchingService
                ->rankResumesForJob(
                    $job,
                    auth()->user(),
                    $limit
                );

        $resumeVersion = null;

        return view(
            'admin.matching.results',
            compact(
                'results',
                'resumeVersion',
                'job',
                'limit'
            ) + [
                'mode' =>
                    'job_to_resumes',
            ]
        );
    }
}