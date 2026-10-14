<?php

namespace App\Modules\Matching\Services;

use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\User;
use App\Modules\ATS\Models\ATSAnalysis;
use App\Modules\ATS\Services\ATSAnalyzerService;
use App\Modules\Jobs\Models\Job;
use Illuminate\Support\Collection;

class MatchingService
{
    public function __construct(
        private readonly ATSAnalyzerService $atsAnalyzer
    ) {
    }

    /**
     * Rank active jobs for one resume version.
     */
    public function rankJobsForResume(
        ResumeVersion $resumeVersion,
        ?User $analyzedBy = null,
        int $limit = 20
    ): Collection {
        $limit = $this->normalizeLimit($limit);

        $jobs = Job::query()
            ->active()
            ->with('skills')
            ->get();

        if ($jobs->isEmpty()) {
            return collect();
        }

        $analyses = $jobs->map(
            fn (Job $job): ATSAnalysis =>
                $this->atsAnalyzer->analyze(
                    $resumeVersion,
                    $job,
                    $analyzedBy
                )
        );

        return $this->sortAnalyses($analyses)
            ->take($limit)
            ->values();
    }

    /**
     * Rank current resume versions for one job.
     */
    public function rankResumesForJob(
        Job $job,
        ?User $analyzedBy = null,
        int $limit = 20
    ): Collection {
        $limit = $this->normalizeLimit($limit);

        $currentVersionIds = Resume::query()
            ->whereNotNull('current_version_id')
            ->pluck('current_version_id')
            ->filter()
            ->unique()
            ->values();

        if ($currentVersionIds->isEmpty()) {
            return collect();
        }

        $resumeVersions = ResumeVersion::query()
            ->whereIn('id', $currentVersionIds)
            ->with([
                'resume',
                'skills',
                'experiences',
                'educations',
                'languages',
            ])
            ->get();

        if ($resumeVersions->isEmpty()) {
            return collect();
        }

        $analyses = $resumeVersions->map(
            fn (ResumeVersion $resumeVersion): ATSAnalysis =>
                $this->atsAnalyzer->analyze(
                    $resumeVersion,
                    $job,
                    $analyzedBy
                )
        );

        return $this->sortAnalyses($analyses)
            ->take($limit)
            ->values();
    }

    /**
     * Sort analyses from strongest match to weakest.
     *
     * Tie-breakers:
     * 1. Overall score
     * 2. Skills score
     * 3. Experience score
     * 4. Education score
     */
    private function sortAnalyses(
        Collection $analyses
    ): Collection {
        return $analyses->sort(
            function (
                ATSAnalysis $first,
                ATSAnalysis $second
            ): int {
                $firstScores = [
                    (float) $first->overall_score,
                    (float) $first->skills_score,
                    (float) $first->experience_score,
                    (float) $first->education_score,
                ];

                $secondScores = [
                    (float) $second->overall_score,
                    (float) $second->skills_score,
                    (float) $second->experience_score,
                    (float) $second->education_score,
                ];

                return $secondScores <=> $firstScores;
            }
        )->values();
    }

    /**
     * Prevent unreasonable matching batch sizes.
     */
    private function normalizeLimit(
        int $limit
    ): int {
        return max(
            1,
            min(100, $limit)
        );
    }
}