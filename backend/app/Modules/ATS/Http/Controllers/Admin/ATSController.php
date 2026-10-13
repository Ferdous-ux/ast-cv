<?php

namespace App\Modules\ATS\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResumeVersion;
use App\Modules\ATS\Models\ATSAnalysis;
use App\Modules\ATS\Services\ATSAnalyzerService;
use App\Modules\Jobs\Models\Job;
use Illuminate\Http\Request;

class ATSController extends Controller
{
    public function index(Request $request)
    {
        $query = ATSAnalysis::with([
            'resumeVersion.resume.user',
            'job',
            'analyzer',
        ])->latest('analyzed_at');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->whereHas('job', function ($jobQuery) use ($search) {
                    $jobQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                })->orWhereHas('resumeVersion.resume', function ($resumeQuery) use ($search) {
                    $resumeQuery->where('title', 'like', "%{$search}%");
                });
            });
        }

        $analyses = $query
            ->paginate(15)
            ->withQueryString();

        $totalAnalyses = ATSAnalysis::count();

        $averageScore = round(
            (float) ATSAnalysis::avg('overall_score'),
            1
        );

        $strongMatches = ATSAnalysis::where(
            'overall_score',
            '>=',
            80
        )->count();

        return view('admin.ats.index', compact(
            'analyses',
            'totalAnalyses',
            'averageScore',
            'strongMatches'
        ));
    }

    public function create()
    {
        $resumeVersions = ResumeVersion::with([
            'resume.user',
        ])
            ->latest('id')
            ->get();

        $jobs = Job::with('skills')
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('admin.ats.create', compact(
            'resumeVersions',
            'jobs'
        ));
    }

    public function store(
        Request $request,
        ATSAnalyzerService $analyzer
    ) {
        $data = $request->validate([
            'resume_version_id' => [
                'required',
                'integer',
                'exists:resume_versions,id',
            ],

            'job_posting_id' => [
                'required',
                'integer',
                'exists:job_postings,id',
            ],
        ]);

        $resumeVersion = ResumeVersion::findOrFail(
            $data['resume_version_id']
        );

        $job = Job::findOrFail(
            $data['job_posting_id']
        );

        $analysis = $analyzer->analyze(
            $resumeVersion,
            $job,
            auth()->user()
        );

        return redirect()
            ->route('admin.ats.show', $analysis)
            ->with(
                'success',
                __('ats.analysis_success')
            );
    }

    public function show(ATSAnalysis $analysis)
    {
        $analysis->load([
            'resumeVersion.resume.user',
            'resumeVersion.skills',
            'resumeVersion.experiences',
            'resumeVersion.educations',
            'job.skills',
            'analyzer',
        ]);

        return view(
            'admin.ats.show',
            compact('analysis')
        );
    }

    public function destroy(ATSAnalysis $analysis)
    {
        $analysis->delete();

        return redirect()
            ->route('admin.ats.index')
            ->with(
                'success',
                __('ats.delete_success')
            );
    }
}