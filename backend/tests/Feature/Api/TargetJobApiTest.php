<?php

namespace Tests\Feature\Api;

use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\User;
use App\Modules\Jobs\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TargetJobApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_target_jobs(): void
    {
        $this->getJson('/api/target-jobs')
            ->assertUnauthorized();

        $this->postJson('/api/target-jobs', [])
            ->assertUnauthorized();
    }

    public function test_user_can_create_target_job(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/target-jobs',
            [
                'title' => 'Senior Laravel Developer',
                'company_name' => 'Example Company',
                'location' => 'Remote',
                'source_url' => 'https://example.com/jobs/laravel',
                'employment_type' => 'full_time',
                'workplace_type' => 'remote',
                'description' =>
                    'We are looking for a Laravel developer with PHP and API experience.',
                'requirements' =>
                    'Laravel, PHP, MySQL, REST API, Docker.',
                'minimum_experience_years' => 3,
                'education_level' => 'bachelor',
            ]
        );

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.source_type',
                'target'
            )
            ->assertJsonPath(
                'data.created_by',
                $user->id
            )
            ->assertJsonPath(
                'data.status',
                'active'
            );

        $this->assertDatabaseHas(
            'job_postings',
            [
                'created_by' => $user->id,
                'source_type' => 'target',
                'title' => 'Senior Laravel Developer',
                'status' => 'active',
            ]
        );
    }

    public function test_target_job_validation_rejects_invalid_data(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(
            '/api/target-jobs',
            [
                'title' => '',
                'source_url' => 'not-a-url',
                'employment_type' => 'invalid',
                'workplace_type' => 'invalid',
            ]
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title',
                'source_url',
                'employment_type',
                'workplace_type',
                'description',
            ]);
    }

    public function test_user_only_lists_own_target_jobs(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $ownJob = $this->createTargetJob(
            $user,
            'My Target Job'
        );

        $this->createTargetJob(
            $otherUser,
            'Other User Target Job'
        );

        $this->createPlatformJob(
            'Public Platform Job'
        );

        $response = $this->getJson(
            '/api/target-jobs'
        );

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.id',
                $ownJob->id
            );
    }

    public function test_user_can_view_own_target_job(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $job = $this->createTargetJob(
            $user,
            'View Target Job'
        );

        $this->getJson(
            "/api/target-jobs/{$job->id}"
        )
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.id',
                $job->id
            );
    }

    public function test_user_can_update_own_target_job(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $job = $this->createTargetJob(
            $user,
            'Old Target Job'
        );

        $response = $this->putJson(
            "/api/target-jobs/{$job->id}",
            [
                'title' => 'Updated Target Job',
                'company_name' => 'Updated Company',
                'location' => 'Sanaa',
                'source_url' =>
                    'https://example.com/jobs/updated',
                'employment_type' => 'full_time',
                'workplace_type' => 'hybrid',
                'description' =>
                    'Updated Laravel and Flutter job description.',
                'requirements' =>
                    'Laravel, Flutter, MySQL.',
                'minimum_experience_years' => 2,
                'education_level' => 'bachelor',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.title',
                'Updated Target Job'
            )
            ->assertJsonPath(
                'data.source_type',
                'target'
            );

        $this->assertDatabaseHas(
            'job_postings',
            [
                'id' => $job->id,
                'created_by' => $user->id,
                'source_type' => 'target',
                'title' => 'Updated Target Job',
            ]
        );
    }

    public function test_user_can_delete_own_target_job(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $job = $this->createTargetJob(
            $user,
            'Delete Target Job'
        );

        $this->deleteJson(
            "/api/target-jobs/{$job->id}"
        )
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing(
            'job_postings',
            [
                'id' => $job->id,
            ]
        );
    }

    public function test_user_cannot_access_another_users_target_job(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $job = $this->createTargetJob(
            $otherUser,
            'Private Target Job'
        );

        $this->getJson(
            "/api/target-jobs/{$job->id}"
        )->assertForbidden();

        $this->putJson(
            "/api/target-jobs/{$job->id}",
            [
                'title' => 'Unauthorized Update',
                'description' => 'Unauthorized',
            ]
        )->assertForbidden();

        $this->deleteJson(
            "/api/target-jobs/{$job->id}"
        )->assertForbidden();
    }

    public function test_platform_job_cannot_be_accessed_as_target_job(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $job = $this->createPlatformJob(
            'Platform Only Job'
        );

        $this->getJson(
            "/api/target-jobs/{$job->id}"
        )->assertNotFound();
    }

    public function test_user_can_analyze_own_target_job_with_own_resume(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $version = $this->createResumeVersionFor(
            $user
        );

        $job = $this->createTargetJob(
            $user,
            'Laravel Target Analysis'
        );

        $response = $this->postJson(
            "/api/target-jobs/{$job->id}/analyze",
            [
                'resume_version_id' =>
                    $version->id,
            ]
        );

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.resume_version_id',
                $version->id
            )
            ->assertJsonPath(
                'data.job_posting_id',
                $job->id
            )
            ->assertJsonPath(
                'data.analyzed_by',
                $user->id
            );

        $this->assertDatabaseHas(
            'ats_analyses',
            [
                'resume_version_id' =>
                    $version->id,

                'job_posting_id' =>
                    $job->id,

                'analyzed_by' =>
                    $user->id,

                'status' =>
                    'completed',
            ]
        );
    }

    public function test_user_cannot_analyze_another_users_target_job(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $version = $this->createResumeVersionFor(
            $user
        );

        $job = $this->createTargetJob(
            $otherUser,
            'Other User Analysis Job'
        );

        $this->postJson(
            "/api/target-jobs/{$job->id}/analyze",
            [
                'resume_version_id' =>
                    $version->id,
            ]
        )->assertForbidden();
    }

    public function test_user_cannot_analyze_target_job_with_another_users_resume(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $otherVersion =
            $this->createResumeVersionFor(
                $otherUser
            );

        $job = $this->createTargetJob(
            $user,
            'Own Target Job'
        );

        $this->postJson(
            "/api/target-jobs/{$job->id}/analyze",
            [
                'resume_version_id' =>
                    $otherVersion->id,
            ]
        )->assertForbidden();
    }

    public function test_standard_ats_endpoint_rejects_target_job(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $version = $this->createResumeVersionFor(
            $user
        );

        $job = $this->createTargetJob(
            $user,
            'Target Job For Standard ATS'
        );

        $this->postJson(
            '/api/ats/analyze',
            [
                'resume_version_id' =>
                    $version->id,

                'job_id' =>
                    $job->id,
            ]
        )->assertUnprocessable();
    }

    public function test_matching_does_not_include_target_jobs(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $version = $this->createResumeVersionFor(
            $user
        );

        $platformJob =
            $this->createPlatformJob(
                'Platform Matching Job'
            );

        $this->createTargetJob(
            $user,
            'Private Target Matching Job'
        );

        $response = $this->postJson(
            "/api/matching/resumes/{$version->id}/jobs",
            [
                'limit' => 20,
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'results_count',
                1
            )
            ->assertJsonPath(
                'data.0.job.id',
                $platformJob->id
            )
            ->assertJsonPath(
                'data.0.job.source_type',
                'platform'
            );
    }

    private function createTargetJob(
        User $user,
        string $title
    ): Job {
        return Job::query()->create([
            'created_by' => $user->id,
            'source_type' => 'target',
            'source_url' =>
                'https://example.com/target-job',

            'title' => $title,
            'company_name' => 'Target Company',
            'location' => 'Remote',

            'employment_type' =>
                'full_time',

            'workplace_type' =>
                'remote',

            'description' =>
                'Laravel PHP REST API backend development.',

            'requirements' =>
                'Laravel PHP MySQL REST API Git.',

            'minimum_experience_years' =>
                2,

            'education_level' =>
                'bachelor',

            'status' =>
                'active',

            'published_at' =>
                null,
        ]);
    }

    private function createPlatformJob(
        string $title
    ): Job {
        return Job::query()->create([
            'source_type' => 'platform',
            'source_url' => null,

            'title' => $title,
            'company_name' => 'Platform Company',
            'location' => 'Remote',

            'employment_type' =>
                'full_time',

            'workplace_type' =>
                'remote',

            'description' =>
                'Laravel PHP REST API backend development.',

            'requirements' =>
                'Laravel PHP MySQL REST API Git.',

            'minimum_experience_years' =>
                2,

            'education_level' =>
                'bachelor',

            'status' =>
                'active',

            'published_at' =>
                now(),
        ]);
    }

    private function createResumeVersionFor(
        User $user
    ): ResumeVersion {
        $resume = Resume::query()->create([
            'user_id' => $user->id,
            'title' => 'ATS Target Resume',
            'status' => 'draft',
        ]);

        $version =
            ResumeVersion::query()->create([
                'resume_id' => $resume->id,
                'version_number' => 1,
                'status' => 'active',

                'summary' =>
                    'Laravel PHP backend developer with MySQL and REST API experience.',

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