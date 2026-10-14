<?php

namespace Tests\Feature\Matching;

use App\Http\Middleware\AdminMiddleware;
use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\Skill;
use App\Models\User;
use App\Modules\Jobs\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMatchingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(
            AdminMiddleware::class
        );

        $this->admin =
            User::factory()->create();
    }

    private function login(): void
    {
        $this->actingAs(
            $this->admin
        );
    }

    private function createResumeVersion(
        ?User $user = null,
        string $summary = 'Laravel developer'
    ): ResumeVersion {
        $user ??= User::factory()->create();

        $resume = Resume::create([
            'user_id' =>
                $user->id,

            'title' =>
                'Developer Resume',

            'status' =>
                'draft',

            'current_version_id' =>
                null,
        ]);

        $version =
            ResumeVersion::create([
                'resume_id' =>
                    $resume->id,

                'version_number' =>
                    1,

                'status' =>
                    'draft',

                'summary' =>
                    $summary,

                'template' =>
                    'default',
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
                        $this->admin->id,

                    'title' =>
                        'Laravel Developer',

                    'company_name' =>
                        'AST Company',

                    'description' =>
                        'Laravel backend developer',

                    'requirements' =>
                        'Laravel',

                    'minimum_experience_years' =>
                        0,

                    'status' =>
                        'active',

                    'published_at' =>
                        now(),
                ],
                $attributes
            )
        );
    }

    public function test_guest_cannot_access_matching(): void
    {
        $response = $this->get(
            route('admin.matching.index')
        );

        $response->assertRedirect();
    }

    public function test_admin_can_open_matching_index(): void
    {
        $this->login();

        $response = $this->get(
            route('admin.matching.index')
        );

        $response
            ->assertOk()
            ->assertViewIs(
                'admin.matching.index'
            );
    }

    public function test_admin_can_match_resume_to_jobs(): void
    {
        $this->login();

        $skill = Skill::create([
            'name' =>
                'Laravel',

            'category' =>
                'Backend',
        ]);

        $resumeVersion =
            $this->createResumeVersion();

        $resumeVersion
            ->skills()
            ->attach(
                $skill->id,
                [
                    'level' =>
                        'advanced',

                    'sort_order' =>
                        0,
                ]
            );

        $strongJob =
            $this->createJob([
                'title' =>
                    'Laravel Engineer',
            ]);

        $strongJob
            ->skills()
            ->attach(
                $skill->id,
                [
                    'importance' =>
                        'required',

                    'weight' =>
                        100,

                    'minimum_level' =>
                        'intermediate',
                ]
            );

        $this->createJob([
            'title' =>
                'General Developer',

            'description' =>
                'General development',

            'requirements' =>
                null,
        ]);

        $response = $this->post(
            route('admin.matching.run'),
            [
                'mode' =>
                    'resume_to_jobs',

                'resume_version_id' =>
                    $resumeVersion->id,

                'limit' =>
                    10,
            ]
        );

        $response
            ->assertOk()
            ->assertViewIs(
                'admin.matching.results'
            )
            ->assertViewHas(
                'mode',
                'resume_to_jobs'
            )
            ->assertViewHas(
                'results',
                function ($results) use (
                    $strongJob
                ) {
                    return
                        $results->isNotEmpty()
                        &&
                        $results->first()
                            ->job_posting_id
                        === $strongJob->id;
                }
            );
    }

    public function test_admin_can_match_job_to_resumes(): void
    {
        $this->login();

        $skill = Skill::create([
            'name' =>
                'Laravel',

            'category' =>
                'Backend',
        ]);

        $strongResume =
            $this->createResumeVersion(
                null,
                'Laravel developer'
            );

        $strongResume
            ->skills()
            ->attach(
                $skill->id,
                [
                    'level' =>
                        'advanced',

                    'sort_order' =>
                        0,
                ]
            );

        $this->createResumeVersion(
            null,
            'Unrelated profile'
        );

        $job =
            $this->createJob();

        $job
            ->skills()
            ->attach(
                $skill->id,
                [
                    'importance' =>
                        'required',

                    'weight' =>
                        100,

                    'minimum_level' =>
                        'intermediate',
                ]
            );

        $response = $this->post(
            route('admin.matching.run'),
            [
                'mode' =>
                    'job_to_resumes',

                'job_id' =>
                    $job->id,

                'limit' =>
                    10,
            ]
        );

        $response
            ->assertOk()
            ->assertViewIs(
                'admin.matching.results'
            )
            ->assertViewHas(
                'mode',
                'job_to_resumes'
            )
            ->assertViewHas(
                'results',
                function ($results) use (
                    $strongResume
                ) {
                    return
                        $results->isNotEmpty()
                        &&
                        $results->first()
                            ->resume_version_id
                        === $strongResume->id;
                }
            );
    }

    public function test_invalid_matching_mode_is_rejected(): void
    {
        $this->login();

        $response = $this
            ->from(
                route('admin.matching.index')
            )
            ->post(
                route('admin.matching.run'),
                [
                    'mode' =>
                        'invalid',

                    'limit' =>
                        10,
                ]
            );

        $response
            ->assertRedirect(
                route('admin.matching.index')
            )
            ->assertSessionHasErrors(
                'mode'
            );
    }

    public function test_inactive_job_cannot_be_used_for_job_matching(): void
    {
        $this->login();

        $job =
            $this->createJob([
                'status' =>
                    'draft',

                'published_at' =>
                    null,
            ]);

        $response = $this
            ->from(
                route('admin.matching.index')
            )
            ->post(
                route('admin.matching.run'),
                [
                    'mode' =>
                        'job_to_resumes',

                    'job_id' =>
                        $job->id,

                    'limit' =>
                        10,
                ]
            );

        $response
            ->assertRedirect(
                route('admin.matching.index')
            )
            ->assertSessionHasErrors(
                'job_id'
            );
    }

    public function test_matching_limit_must_be_valid(): void
    {
        $this->login();

        $resumeVersion =
            $this->createResumeVersion();

        $response = $this
            ->from(
                route('admin.matching.index')
            )
            ->post(
                route('admin.matching.run'),
                [
                    'mode' =>
                        'resume_to_jobs',

                    'resume_version_id' =>
                        $resumeVersion->id,

                    'limit' =>
                        101,
                ]
            );

        $response
            ->assertRedirect(
                route('admin.matching.index')
            )
            ->assertSessionHasErrors(
                'limit'
            );
    }
}