<?php

namespace Tests\Feature\Api;

use App\Models\Language;
use App\Models\Resume;
use App\Models\ResumeTemplate;
use App\Models\ResumeVersion;
use App\Models\Skill;
use App\Models\User;
use App\Modules\ATS\Models\ATSAnalysis;
use App\Modules\Jobs\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAppApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_protected_user_api(): void
    {
        $this->getJson('/api/reference/templates')
            ->assertUnauthorized();

        $this->getJson('/api/ats/analyses')
            ->assertUnauthorized();

        $this->postJson('/api/matching/resumes/1/jobs')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_get_active_templates(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        ResumeTemplate::query()->create([
            'key' => 'classic-test',
            'name' => 'Classic Test',
            'description' => 'ATS friendly template',
            'thumbnail' => null,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ResumeTemplate::query()->create([
            'key' => 'hidden-test',
            'name' => 'Hidden Test',
            'description' => null,
            'thumbnail' => null,
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $response = $this->getJson(
            '/api/reference/templates'
        );

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.key',
                'classic-test'
            );
    }

    public function test_authenticated_user_can_get_skills(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Skill::query()->create([
            'name' => 'Laravel Test',
            'category' => 'Backend',
        ]);

        Skill::query()->create([
            'name' => 'Flutter Test',
            'category' => 'Mobile',
        ]);

        $response = $this->getJson(
            '/api/reference/skills'
        );

        $response
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertGreaterThanOrEqual(
            2,
            count($response->json('data'))
        );
    }

    public function test_authenticated_user_can_get_only_active_languages(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Language::query()->create([
            'code' => 'zz-test-active',
            'name' => 'Test Active Language',
            'native_name' => 'Test Active Language',
            'direction' => 'ltr',
            'is_active' => true,
        ]);

        Language::query()->create([
            'code' => 'zz-test-inactive',
            'name' => 'Test Inactive Language',
            'native_name' => 'Test Inactive Language',
            'direction' => 'ltr',
            'is_active' => false,
        ]);

        $response = $this->getJson(
            '/api/reference/languages'
        );

        $response
            ->assertOk()
            ->assertJsonPath('success', true);

        $codes = collect(
            $response->json('data')
        )->pluck('code');

        $this->assertTrue(
            $codes->contains('zz-test-active')
        );

        $this->assertFalse(
            $codes->contains('zz-test-inactive')
        );
    }

    public function test_user_can_run_ats_analysis_for_own_resume(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        [
            $resume,
            $version,
        ] = $this->createResumeFor($user);

        $skill = Skill::query()->create([
            'name' => 'Laravel ATS Test',
            'category' => 'Backend',
        ]);

        $version->skills()->attach(
            $skill->id,
            [
                'level' => 'advanced',
                'sort_order' => 1,
            ]
        );

        $job = $this->createActiveJob(
            'Laravel Developer Test'
        );

        $job->skills()->attach(
            $skill->id,
            [
                'importance' => 'required',
                'weight' => 100,
                'minimum_level' => 'intermediate',
            ]
        );

        $response = $this->postJson(
            '/api/ats/analyze',
            [
                'resume_version_id' => $version->id,
                'job_id' => $job->id,
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
                'resume_version_id' => $version->id,
                'job_posting_id' => $job->id,
                'analyzed_by' => $user->id,
                'status' => 'completed',
            ]
        );
    }

    public function test_user_cannot_analyze_another_users_resume(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        [
            $resume,
            $version,
        ] = $this->createResumeFor($otherUser);

        $job = $this->createActiveJob(
            'Protected Job Test'
        );

        $this->postJson(
            '/api/ats/analyze',
            [
                'resume_version_id' => $version->id,
                'job_id' => $job->id,
            ]
        )->assertForbidden();
    }

    public function test_user_cannot_analyze_inactive_job(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        [
            $resume,
            $version,
        ] = $this->createResumeFor($user);

        $job = Job::query()->create([
            'title' => 'Draft Job Test',
            'company_name' => 'Test Company',
            'location' => 'Remote',
            'employment_type' => 'full_time',
            'workplace_type' => 'remote',
            'description' => 'Draft job description.',
            'requirements' => 'Testing requirements.',
            'minimum_experience_years' => 1,
            'education_level' => 'bachelor',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->postJson(
            '/api/ats/analyze',
            [
                'resume_version_id' => $version->id,
                'job_id' => $job->id,
            ]
        )->assertUnprocessable();
    }

    public function test_user_can_list_only_own_ats_analyses(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        [
            $resume,
            $version,
        ] = $this->createResumeFor($user);

        [
            $otherResume,
            $otherVersion,
        ] = $this->createResumeFor($otherUser);

        $job = $this->createActiveJob(
            'Analysis List Job'
        );

        ATSAnalysis::query()->create([
            'resume_version_id' => $version->id,
            'job_posting_id' => $job->id,
            'analyzed_by' => $user->id,
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
            'status' => 'completed',
            'analyzed_at' => now(),
        ]);

        ATSAnalysis::query()->create([
            'resume_version_id' => $otherVersion->id,
            'job_posting_id' => $job->id,
            'analyzed_by' => $otherUser->id,
            'overall_score' => 70,
            'skills_score' => 70,
            'experience_score' => 70,
            'education_score' => 70,
            'keywords_score' => 70,
            'structure_score' => 70,
            'matched_skills' => [],
            'missing_skills' => [],
            'matched_keywords' => [],
            'missing_keywords' => [],
            'recommendations' => [],
            'details' => [],
            'status' => 'completed',
            'analyzed_at' => now(),
        ]);

        $response = $this->getJson(
            '/api/ats/analyses'
        );

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath(
                'data.data.0.analyzed_by',
                $user->id
            );
    }

    public function test_user_can_view_own_analysis(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        [
            $resume,
            $version,
        ] = $this->createResumeFor($user);

        $job = $this->createActiveJob(
            'Show Analysis Job'
        );

        $analysis = ATSAnalysis::query()->create([
            'resume_version_id' => $version->id,
            'job_posting_id' => $job->id,
            'analyzed_by' => $user->id,
            'overall_score' => 90,
            'skills_score' => 90,
            'experience_score' => 90,
            'education_score' => 90,
            'keywords_score' => 90,
            'structure_score' => 90,
            'matched_skills' => [],
            'missing_skills' => [],
            'matched_keywords' => [],
            'missing_keywords' => [],
            'recommendations' => [],
            'details' => [],
            'status' => 'completed',
            'analyzed_at' => now(),
        ]);

        $this->getJson(
            "/api/ats/analyses/{$analysis->id}"
        )
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.id',
                $analysis->id
            );
    }

    public function test_user_cannot_view_another_users_analysis(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        [
            $resume,
            $version,
        ] = $this->createResumeFor($otherUser);

        $job = $this->createActiveJob(
            'Private Analysis Job'
        );

        $analysis = ATSAnalysis::query()->create([
            'resume_version_id' => $version->id,
            'job_posting_id' => $job->id,
            'analyzed_by' => $otherUser->id,
            'overall_score' => 75,
            'skills_score' => 75,
            'experience_score' => 75,
            'education_score' => 75,
            'keywords_score' => 75,
            'structure_score' => 75,
            'matched_skills' => [],
            'missing_skills' => [],
            'matched_keywords' => [],
            'missing_keywords' => [],
            'recommendations' => [],
            'details' => [],
            'status' => 'completed',
            'analyzed_at' => now(),
        ]);

        $this->getJson(
            "/api/ats/analyses/{$analysis->id}"
        )->assertForbidden();
    }

    public function test_matching_returns_only_active_jobs_for_own_resume(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        [
            $resume,
            $version,
        ] = $this->createResumeFor($user);

        $activeJob = $this->createActiveJob(
            'Active Matching Job'
        );

        Job::query()->create([
            'title' => 'Draft Matching Job',
            'company_name' => 'Draft Company',
            'location' => 'Remote',
            'employment_type' => 'full_time',
            'workplace_type' => 'remote',
            'description' => 'Draft job description.',
            'requirements' => 'Draft requirements.',
            'minimum_experience_years' => 1,
            'education_level' => 'bachelor',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->postJson(
            "/api/matching/resumes/{$version->id}/jobs",
            [
                'limit' => 20,
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('results_count', 1)
            ->assertJsonPath(
                'data.0.job.id',
                $activeJob->id
            );
    }

    public function test_user_cannot_run_matching_for_another_users_resume(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        [
            $resume,
            $version,
        ] = $this->createResumeFor($otherUser);

        $this->createActiveJob(
            'Unauthorized Matching Job'
        );

        $this->postJson(
            "/api/matching/resumes/{$version->id}/jobs",
            [
                'limit' => 20,
            ]
        )->assertForbidden();
    }

    public function test_matching_validates_limit(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        [
            $resume,
            $version,
        ] = $this->createResumeFor($user);

        $this->postJson(
            "/api/matching/resumes/{$version->id}/jobs",
            [
                'limit' => 101,
            ]
        )->assertUnprocessable();
    }

    private function createResumeFor(
        User $user
    ): array {
        $resume = Resume::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Resume',
            'status' => 'draft',
        ]);

        $version = ResumeVersion::query()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
            'status' => 'active',
            'summary' => 'Laravel backend developer with API experience.',
            'template' => 'classic',
        ]);

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        return [
            $resume,
            $version,
        ];
    }

    private function createActiveJob(
        string $title
    ): Job {
        return Job::query()->create([
            'title' => $title,
            'company_name' => 'Test Company',
            'location' => 'Remote',
            'employment_type' => 'full_time',
            'workplace_type' => 'remote',
            'description' =>
                'Laravel backend API development.',
            'requirements' =>
                'Laravel PHP REST API MySQL.',
            'minimum_experience_years' => 1,
            'education_level' => 'bachelor',
            'status' => 'active',
            'published_at' => now(),
        ]);
    }
}