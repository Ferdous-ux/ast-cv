<?php

namespace Tests\Feature\Matching;

use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\Skill;
use App\Models\User;
use App\Modules\Jobs\Models\Job;
use App\Modules\Matching\Services\MatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private MatchingService $matchingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user =
            User::factory()->create();

        $this->matchingService =
            app(MatchingService::class);
    }

    private function createCurrentResumeVersion(
        User $user,
        string $title = 'Developer Resume',
        ?string $summary = null
    ): ResumeVersion {
        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => $title,
            'status' => 'draft',
            'current_version_id' => null,
        ]);

        $version = ResumeVersion::create([
            'resume_id' => $resume->id,
            'version_number' => 1,
            'status' => 'draft',
            'summary' => $summary,
            'template' => 'default',
        ]);

        $resume->update([
            'current_version_id' =>
                $version->id,
        ]);

        return $version;
    }

    private function createJob(
        array $attributes = []
    ): Job {
        return Job::create(
            array_merge(
                [
                    'created_by' =>
                        $this->user->id,

                    'title' =>
                        'Backend Developer',

                    'company_name' =>
                        'AST Company',

                    'location' =>
                        'Sanaa',

                    'employment_type' =>
                        'full_time',

                    'workplace_type' =>
                        'onsite',

                    'description' =>
                        'Backend developer',

                    'requirements' =>
                        null,

                    'minimum_experience_years' =>
                        0,

                    'education_level' =>
                        null,

                    'status' =>
                        'active',

                    'published_at' =>
                        now(),
                ],
                $attributes
            )
        );
    }

    public function test_resume_is_matched_to_active_jobs_and_ranked_by_score(): void
    {
        $laravel = Skill::create([
            'name' => 'Laravel',
            'category' => 'Backend',
        ]);

        $python = Skill::create([
            'name' => 'Python',
            'category' => 'Backend',
        ]);

        $resumeVersion =
            $this->createCurrentResumeVersion(
                $this->user,
                'Laravel Resume',
                'Laravel backend developer'
            );

        $resumeVersion
            ->skills()
            ->attach(
                $laravel->id,
                [
                    'level' => 'advanced',
                    'sort_order' => 0,
                ]
            );

        $strongJob = $this->createJob([
            'title' =>
                'Laravel Developer',

            'description' =>
                'Laravel',

            'requirements' =>
                'Laravel',
        ]);

        $strongJob
            ->skills()
            ->attach(
                $laravel->id,
                [
                    'importance' =>
                        'required',

                    'weight' =>
                        100,

                    'minimum_level' =>
                        'intermediate',
                ]
            );

        $weakJob = $this->createJob([
            'title' =>
                'Python Developer',

            'description' =>
                'Python',

            'requirements' =>
                'Python',
        ]);

        $weakJob
            ->skills()
            ->attach(
                $python->id,
                [
                    'importance' =>
                        'required',

                    'weight' =>
                        100,

                    'minimum_level' =>
                        'intermediate',
                ]
            );

        $results =
            $this->matchingService
                ->rankJobsForResume(
                    $resumeVersion,
                    $this->user
                );

        $this->assertCount(
            2,
            $results
        );

        $this->assertSame(
            $strongJob->id,
            $results->first()
                ->job_posting_id
        );

        $this->assertGreaterThan(
            $results->last()
                ->overall_score,
            $results->first()
                ->overall_score
        );

        $this->assertDatabaseCount(
            'ats_analyses',
            2
        );
    }

    public function test_draft_and_closed_jobs_are_not_matched_to_resume(): void
    {
        $resumeVersion =
            $this->createCurrentResumeVersion(
                $this->user
            );

        $activeJob =
            $this->createJob([
                'title' =>
                    'Active Job',

                'status' =>
                    'active',
            ]);

        $this->createJob([
            'title' =>
                'Draft Job',

            'status' =>
                'draft',

            'published_at' =>
                null,
        ]);

        $this->createJob([
            'title' =>
                'Closed Job',

            'status' =>
                'closed',

            'published_at' =>
                null,
        ]);

        $results =
            $this->matchingService
                ->rankJobsForResume(
                    $resumeVersion,
                    $this->user
                );

        $this->assertCount(
            1,
            $results
        );

        $this->assertSame(
            $activeJob->id,
            $results->first()
                ->job_posting_id
        );

        $this->assertDatabaseCount(
            'ats_analyses',
            1
        );
    }

    public function test_job_can_rank_current_resume_versions(): void
    {
        $laravel = Skill::create([
            'name' =>
                'Laravel',

            'category' =>
                'Backend',
        ]);

        $python = Skill::create([
            'name' =>
                'Python',

            'category' =>
                'Backend',
        ]);

        $strongUser =
            User::factory()->create();

        $weakUser =
            User::factory()->create();

        $strongResume =
            $this->createCurrentResumeVersion(
                $strongUser,
                'Laravel Resume',
                'Laravel developer'
            );

        $weakResume =
            $this->createCurrentResumeVersion(
                $weakUser,
                'Python Resume',
                'Python developer'
            );

        $strongResume
            ->skills()
            ->attach(
                $laravel->id,
                [
                    'level' =>
                        'advanced',

                    'sort_order' =>
                        0,
                ]
            );

        $weakResume
            ->skills()
            ->attach(
                $python->id,
                [
                    'level' =>
                        'advanced',

                    'sort_order' =>
                        0,
                ]
            );

        $job = $this->createJob([
            'title' =>
                'Laravel Engineer',

            'description' =>
                'Laravel',

            'requirements' =>
                'Laravel',
        ]);

        $job->skills()->attach(
            $laravel->id,
            [
                'importance' =>
                    'required',

                'weight' =>
                    100,

                'minimum_level' =>
                    'intermediate',
            ]
        );

        $results =
            $this->matchingService
                ->rankResumesForJob(
                    $job,
                    $this->user
                );

        $this->assertCount(
            2,
            $results
        );

        $this->assertSame(
            $strongResume->id,
            $results->first()
                ->resume_version_id
        );

        $this->assertGreaterThan(
            $results->last()
                ->overall_score,
            $results->first()
                ->overall_score
        );
    }

    public function test_matching_limit_is_respected(): void
    {
        $resumeVersion =
            $this->createCurrentResumeVersion(
                $this->user
            );

        $this->createJob([
            'title' =>
                'Job One',
        ]);

        $this->createJob([
            'title' =>
                'Job Two',
        ]);

        $this->createJob([
            'title' =>
                'Job Three',
        ]);

        $results =
            $this->matchingService
                ->rankJobsForResume(
                    $resumeVersion,
                    $this->user,
                    2
                );

        $this->assertCount(
            2,
            $results
        );
    }

    public function test_matching_returns_empty_collection_when_no_active_jobs_exist(): void
    {
        $resumeVersion =
            $this->createCurrentResumeVersion(
                $this->user
            );

        $this->createJob([
            'title' =>
                'Draft Job',

            'status' =>
                'draft',

            'published_at' =>
                null,
        ]);

        $results =
            $this->matchingService
                ->rankJobsForResume(
                    $resumeVersion,
                    $this->user
                );

        $this->assertTrue(
            $results->isEmpty()
        );

        $this->assertDatabaseCount(
            'ats_analyses',
            0
        );
    }
}