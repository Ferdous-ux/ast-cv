<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleware;
use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\User;
use App\Modules\ATS\Models\ATSAnalysis;
use App\Modules\Jobs\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TargetJobAdminIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(
            AdminMiddleware::class
        );
    }

    public function test_admin_jobs_index_hides_target_jobs(): void
    {
        $admin = User::factory()->create();

        $platformJob = $this->createJob(
            'Visible Platform Job',
            'platform'
        );

        $this->createJob(
            'Hidden Private Target Job',
            'target',
            User::factory()->create()
        );

        $response = $this
            ->actingAs($admin)
            ->get(
                route('admin.jobs.index')
            );

        $response
            ->assertOk()
            ->assertSee(
                $platformJob->title
            )
            ->assertDontSee(
                'Hidden Private Target Job'
            );
    }

    public function test_admin_cannot_open_target_job_as_platform_job(): void
    {
        $admin = User::factory()->create();
        $owner = User::factory()->create();

        $targetJob = $this->createJob(
            'Private Target Job',
            'target',
            $owner
        );

        $this
            ->actingAs($admin)
            ->get(
                route(
                    'admin.jobs.show',
                    $targetJob
                )
            )
            ->assertNotFound();
    }

    public function test_admin_ats_create_does_not_list_target_jobs(): void
    {
        $admin = User::factory()->create();

        $platformJob = $this->createJob(
            'ATS Platform Job',
            'platform'
        );

        $this->createJob(
            'ATS Private Target Job',
            'target',
            User::factory()->create()
        );

        $response = $this
            ->actingAs($admin)
            ->get(
                route('admin.ats.create')
            );

        $response
            ->assertOk()
            ->assertSee(
                $platformJob->title
            )
            ->assertDontSee(
                'ATS Private Target Job'
            );
    }

    public function test_admin_ats_cannot_analyze_target_job(): void
    {
        $admin = User::factory()->create();
        $owner = User::factory()->create();

        $version =
            $this->createResumeVersionFor(
                $owner
            );

        $targetJob = $this->createJob(
            'Protected Target ATS Job',
            'target',
            $owner
        );

        $response = $this
            ->actingAs($admin)
            ->from(
                route('admin.ats.create')
            )
            ->post(
                route('admin.ats.store'),
                [
                    'resume_version_id' =>
                        $version->id,

                    'job_posting_id' =>
                        $targetJob->id,
                ]
            );

        $response
            ->assertRedirect(
                route('admin.ats.create')
            )
            ->assertSessionHasErrors(
                'job_posting_id'
            );

        $this->assertDatabaseMissing(
            'ats_analyses',
            [
                'job_posting_id' =>
                    $targetJob->id,
            ]
        );
    }

    public function test_admin_cannot_open_target_job_analysis(): void
    {
        $admin = User::factory()->create();
        $owner = User::factory()->create();

        $version =
            $this->createResumeVersionFor(
                $owner
            );

        $targetJob = $this->createJob(
            'Private Analysis Job',
            'target',
            $owner
        );

        $analysis =
            ATSAnalysis::query()->create([
                'resume_version_id' =>
                    $version->id,

                'job_posting_id' =>
                    $targetJob->id,

                'analyzed_by' =>
                    $owner->id,

                'overall_score' => 80,
                'skills_score' => 80,
                'experience_score' => 80,
                'education_score' => 80,
                'keywords_score' => 80,
                'structure_score' => 80,

                'matched_skills' => [],
                'missing_skills' => [],
                'matched_keywords' => [],
                'missing_keywords' => [],
                'recommendations' => [],
                'details' => [],

                'status' =>
                    'completed',

                'analyzed_at' =>
                    now(),
            ]);

        $this
            ->actingAs($admin)
            ->get(
                route(
                    'admin.ats.show',
                    $analysis
                )
            )
            ->assertNotFound();
    }

    private function createJob(
        string $title,
        string $sourceType,
        ?User $owner = null
    ): Job {
        return Job::query()->create([
            'created_by' =>
                $owner?->id,

            'source_type' =>
                $sourceType,

            'source_url' =>
                $sourceType === 'target'
                    ? 'https://example.com/job'
                    : null,

            'title' =>
                $title,

            'company_name' =>
                'Test Company',

            'location' =>
                'Remote',

            'employment_type' =>
                'full_time',

            'workplace_type' =>
                'remote',

            'description' =>
                'Laravel PHP backend development.',

            'requirements' =>
                'Laravel PHP MySQL API.',

            'minimum_experience_years' =>
                2,

            'education_level' =>
                'bachelor',

            'status' =>
                'active',

            'published_at' =>
                $sourceType === 'platform'
                    ? now()
                    : null,
        ]);
    }

    private function createResumeVersionFor(
        User $user
    ): ResumeVersion {
        $resume = Resume::query()->create([
            'user_id' => $user->id,
            'title' => 'Isolation Resume',
            'status' => 'draft',
        ]);

        $version =
            ResumeVersion::query()->create([
                'resume_id' =>
                    $resume->id,

                'version_number' =>
                    1,

                'status' =>
                    'active',

                'summary' =>
                    'Laravel backend developer.',

                'template' =>
                    'classic',
            ]);

        $resume->update([
            'current_version_id' =>
                $version->id,
        ]);

        return $version;
    }
}