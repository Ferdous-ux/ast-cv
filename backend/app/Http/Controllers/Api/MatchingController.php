<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResumeVersion;
use App\Modules\ATS\Models\ATSAnalysis;
use App\Modules\Matching\Services\MatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    public function jobsForResume(
        Request $request,
        ResumeVersion $version,
        MatchingService $matchingService
    ): JsonResponse {
        $validated = $request->validate([
            'limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $version->loadMissing('resume');

        abort_unless(
            $version->resume
                && $version->resume->user_id === $request->user()->id,
            403
        );

        $analyses = $matchingService->rankJobsForResume(
            $version,
            $request->user(),
            $validated['limit'] ?? 20
        );

        $analyses->each(
            fn (ATSAnalysis $analysis) =>
                $analysis->load('job.skills')
        );

        $results = $analyses
            ->values()
            ->map(
                function (
                    ATSAnalysis $analysis,
                    int $index
                ): array {
                    return [
                        'rank' => $index + 1,
                        'analysis_id' => $analysis->id,

                        'job' => $analysis->job,

                        'overall_score' =>
                            $analysis->overall_score,

                        'skills_score' =>
                            $analysis->skills_score,

                        'experience_score' =>
                            $analysis->experience_score,

                        'education_score' =>
                            $analysis->education_score,

                        'keywords_score' =>
                            $analysis->keywords_score,

                        'structure_score' =>
                            $analysis->structure_score,

                        'matched_skills' =>
                            $analysis->matched_skills,

                        'missing_skills' =>
                            $analysis->missing_skills,

                        'recommendations' =>
                            $analysis->recommendations,
                    ];
                }
            );

        return response()->json([
            'success' => true,
            'resume_version_id' => $version->id,
            'results_count' => $results->count(),
            'data' => $results,
        ]);
    }
}