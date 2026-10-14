<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleware;
use App\Models\Skill;
use App\Models\User;
use App\Modules\Jobs\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJobTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * نعطل AdminMiddleware فقط.
         *
         * Middleware الخاص بـ auth يبقى فعالًا
         * حتى نختبر حماية Jobs من الضيوف.
         */
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

    private function createJob(
        array $attributes = []
    ): Job {
        return Job::create(
            array_merge(
                [
                    'created_by' =>
                        $this->admin->id,

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
                        'Backend developer position.',

                    'requirements' =>
                        'Laravel and MySQL.',

                    'minimum_experience_years' =>
                        2,

                    'education_level' =>
                        'bachelor',

                    'status' =>
                        'draft',

                    'published_at' =>
                        null,
                ],
                $attributes
            )
        );
    }

    public function test_guest_cannot_access_jobs(): void
    {
        $response = $this->get(
            route('admin.jobs.index')
        );

        $response->assertRedirect();
    }

    public function test_admin_can_open_jobs_index(): void
    {
        $this->login();

        $job = $this->createJob();

        $response = $this->get(
            route('admin.jobs.index')
        );

        $response
            ->assertOk()
            ->assertViewIs(
                'admin.jobs.index'
            )
            ->assertSee(
                $job->title
            );
    }

    public function test_admin_can_open_create_job_page(): void
    {
        $this->login();

        $response = $this->get(
            route('admin.jobs.create')
        );

        $response
            ->assertOk()
            ->assertViewIs(
                'admin.jobs.create'
            );
    }

    public function test_admin_can_create_job_without_skills(): void
    {
        $this->login();

        $response = $this->post(
            route('admin.jobs.store'),
            [
                'title' =>
                    'PHP Developer',

                'company_name' =>
                    'Example Company',

                'location' =>
                    'Remote',

                'employment_type' =>
                    'full_time',

                'workplace_type' =>
                    'remote',

                'description' =>
                    'Build backend systems.',

                'requirements' =>
                    'PHP experience.',

                'minimum_experience_years' =>
                    2,

                'education_level' =>
                    'bachelor',

                'status' =>
                    'draft',
            ]
        );

        $response->assertRedirect(
            route('admin.jobs.index')
        );

        $this->assertDatabaseHas(
            'job_postings',
            [
                'title' =>
                    'PHP Developer',

                'created_by' =>
                    $this->admin->id,

                'status' =>
                    'draft',
            ]
        );

        $job = Job::query()
            ->where(
                'title',
                'PHP Developer'
            )
            ->firstOrFail();

        $this->assertNull(
            $job->published_at
        );

        $this->assertSame(
            0,
            $job->skills()->count()
        );
    }

    public function test_admin_can_create_active_job_with_skills(): void
    {
        $this->login();

        $laravel = Skill::create([
            'name' =>
                'Laravel',

            'category' =>
                'Backend',
        ]);

        $mysql = Skill::create([
            'name' =>
                'MySQL',

            'category' =>
                'Database',
        ]);

        $response = $this->post(
            route('admin.jobs.store'),
            [
                'title' =>
                    'Senior Laravel Developer',

                'company_name' =>
                    'AST Company',

                'location' =>
                    'Sanaa',

                'employment_type' =>
                    'full_time',

                'workplace_type' =>
                    'hybrid',

                'description' =>
                    'Senior backend role.',

                'requirements' =>
                    'Laravel and MySQL.',

                'minimum_experience_years' =>
                    4,

                'education_level' =>
                    'bachelor',

                'status' =>
                    'active',

                'skill_ids' => [
                    $laravel->id,
                    $mysql->id,
                ],

                'importance' => [
                    $laravel->id =>
                        'required',

                    $mysql->id =>
                        'preferred',
                ],

                'weight' => [
                    $laravel->id =>
                        100,

                    $mysql->id =>
                        60,
                ],

                'minimum_level' => [
                    $laravel->id =>
                        'advanced',

                    $mysql->id =>
                        'intermediate',
                ],
            ]
        );

        $response->assertRedirect(
            route('admin.jobs.index')
        );

        $job = Job::query()
            ->where(
                'title',
                'Senior Laravel Developer'
            )
            ->firstOrFail();

        $this->assertSame(
            $this->admin->id,
            $job->created_by
        );

        $this->assertNotNull(
            $job->published_at
        );

        $this->assertDatabaseHas(
            'job_skills',
            [
                'job_id' =>
                    $job->id,

                'skill_id' =>
                    $laravel->id,

                'importance' =>
                    'required',

                'weight' =>
                    100,

                'minimum_level' =>
                    'advanced',
            ]
        );

        $this->assertDatabaseHas(
            'job_skills',
            [
                'job_id' =>
                    $job->id,

                'skill_id' =>
                    $mysql->id,

                'importance' =>
                    'preferred',

                'weight' =>
                    60,

                'minimum_level' =>
                    'intermediate',
            ]
        );
    }

    public function test_job_validation_rejects_invalid_data(): void
    {
        $this->login();

        $response = $this
            ->from(
                route(
                    'admin.jobs.create'
                )
            )
            ->post(
                route(
                    'admin.jobs.store'
                ),
                [
                    'title' => '',
                    'description' => '',
                    'status' =>
                        'invalid_status',

                    'employment_type' =>
                        'invalid_type',

                    'workplace_type' =>
                        'invalid_workplace',

                    'minimum_experience_years' =>
                        51,
                ]
            );

        $response
            ->assertRedirect(
                route(
                    'admin.jobs.create'
                )
            )
            ->assertSessionHasErrors([
                'title',
                'description',
                'status',
                'employment_type',
                'workplace_type',
                'minimum_experience_years',
            ]);
    }

    public function test_job_skill_validation_rejects_invalid_values(): void
    {
        $this->login();

        $skill = Skill::create([
            'name' =>
                'PHP',

            'category' =>
                'Backend',
        ]);

        $response = $this
            ->from(
                route(
                    'admin.jobs.create'
                )
            )
            ->post(
                route(
                    'admin.jobs.store'
                ),
                [
                    'title' =>
                        'Developer',

                    'description' =>
                        'Developer position.',

                    'status' =>
                        'draft',

                    'skill_ids' => [
                        $skill->id,
                    ],

                    'importance' => [
                        $skill->id =>
                            'mandatory',
                    ],

                    'weight' => [
                        $skill->id =>
                            101,
                    ],

                    'minimum_level' => [
                        $skill->id =>
                            'master',
                    ],
                ]
            );

        $response
            ->assertRedirect(
                route(
                    'admin.jobs.create'
                )
            )
            ->assertSessionHasErrors([
                "importance.{$skill->id}",
                "weight.{$skill->id}",
                "minimum_level.{$skill->id}",
            ]);
    }

    public function test_admin_can_view_job(): void
    {
        $this->login();

        $job = $this->createJob([
            'title' =>
                'Visible Job',
        ]);

        $response = $this->get(
            route(
                'admin.jobs.show',
                $job
            )
        );

        $response
            ->assertOk()
            ->assertViewIs(
                'admin.jobs.show'
            )
            ->assertSee(
                'Visible Job'
            );
    }

    public function test_admin_can_open_edit_job_page(): void
    {
        $this->login();

        $job = $this->createJob();

        $response = $this->get(
            route(
                'admin.jobs.edit',
                $job
            )
        );

        $response
            ->assertOk()
            ->assertViewIs(
                'admin.jobs.edit'
            );
    }

    public function test_admin_can_update_job_and_sync_skills(): void
    {
        $this->login();

        $oldSkill = Skill::create([
            'name' =>
                'Old Skill',

            'category' =>
                'Backend',
        ]);

        $newSkill = Skill::create([
            'name' =>
                'New Skill',

            'category' =>
                'Backend',
        ]);

        $job = $this->createJob();

        $job->skills()->attach(
            $oldSkill->id,
            [
                'importance' =>
                    'required',

                'weight' =>
                    100,

                'minimum_level' =>
                    'beginner',
            ]
        );

        $response = $this->put(
            route(
                'admin.jobs.update',
                $job
            ),
            [
                'title' =>
                    'Updated Developer',

                'company_name' =>
                    'Updated Company',

                'location' =>
                    'Aden',

                'employment_type' =>
                    'contract',

                'workplace_type' =>
                    'remote',

                'description' =>
                    'Updated description.',

                'requirements' =>
                    'Updated requirements.',

                'minimum_experience_years' =>
                    5,

                'education_level' =>
                    'master',

                'status' =>
                    'active',

                'skill_ids' => [
                    $newSkill->id,
                ],

                'importance' => [
                    $newSkill->id =>
                        'preferred',
                ],

                'weight' => [
                    $newSkill->id =>
                        70,
                ],

                'minimum_level' => [
                    $newSkill->id =>
                        'advanced',
                ],
            ]
        );

        $response->assertRedirect(
            route('admin.jobs.index')
        );

        $job->refresh();

        $this->assertSame(
            'Updated Developer',
            $job->title
        );

        $this->assertSame(
            'active',
            $job->status
        );

        $this->assertNotNull(
            $job->published_at
        );

        $this->assertDatabaseMissing(
            'job_skills',
            [
                'job_id' =>
                    $job->id,

                'skill_id' =>
                    $oldSkill->id,
            ]
        );

        $this->assertDatabaseHas(
            'job_skills',
            [
                'job_id' =>
                    $job->id,

                'skill_id' =>
                    $newSkill->id,

                'importance' =>
                    'preferred',

                'weight' =>
                    70,

                'minimum_level' =>
                    'advanced',
            ]
        );
    }

    public function test_closing_active_job_clears_published_at(): void
    {
        $this->login();

        $job = $this->createJob([
            'status' =>
                'active',

            'published_at' =>
                now(),
        ]);

        $response = $this->put(
            route(
                'admin.jobs.update',
                $job
            ),
            [
                'title' =>
                    $job->title,

                'company_name' =>
                    $job->company_name,

                'location' =>
                    $job->location,

                'employment_type' =>
                    $job->employment_type,

                'workplace_type' =>
                    $job->workplace_type,

                'description' =>
                    $job->description,

                'requirements' =>
                    $job->requirements,

                'minimum_experience_years' =>
                    $job->minimum_experience_years,

                'education_level' =>
                    $job->education_level,

                'status' =>
                    'closed',
            ]
        );

        $response->assertRedirect(
            route('admin.jobs.index')
        );

        $job->refresh();

        $this->assertSame(
            'closed',
            $job->status
        );

        $this->assertNull(
            $job->published_at
        );
    }

    public function test_admin_can_search_jobs_by_title_company_and_location(): void
    {
        $this->login();

        $this->createJob([
            'title' =>
                'Unique Laravel Engineer',

            'company_name' =>
                'Alpha Company',

            'location' =>
                'Sanaa',
        ]);

        $this->createJob([
            'title' =>
                'Python Developer',

            'company_name' =>
                'Beta Corporation',

            'location' =>
                'Aden',
        ]);

        $response = $this->get(
            route(
                'admin.jobs.index',
                [
                    'search' =>
                        'Laravel',
                ]
            )
        );

        $response
            ->assertOk()
            ->assertSee(
                'Unique Laravel Engineer'
            )
            ->assertDontSee(
                'Python Developer'
            );

        $response = $this->get(
            route(
                'admin.jobs.index',
                [
                    'search' =>
                        'Beta Corporation',
                ]
            )
        );

        $response
            ->assertOk()
            ->assertSee(
                'Python Developer'
            )
            ->assertDontSee(
                'Unique Laravel Engineer'
            );

        $response = $this->get(
            route(
                'admin.jobs.index',
                [
                    'search' =>
                        'Sanaa',
                ]
            )
        );

        $response
            ->assertOk()
            ->assertSee(
                'Unique Laravel Engineer'
            )
            ->assertDontSee(
                'Python Developer'
            );
    }

    public function test_admin_can_filter_jobs_by_status(): void
    {
        $this->login();

        $this->createJob([
            'title' =>
                'Draft Position',

            'status' =>
                'draft',
        ]);

        $this->createJob([
            'title' =>
                'Active Position',

            'status' =>
                'active',

            'published_at' =>
                now(),
        ]);

        $response = $this->get(
            route(
                'admin.jobs.index',
                [
                    'status' =>
                        'active',
                ]
            )
        );

        $response
            ->assertOk()
            ->assertSee(
                'Active Position'
            )
            ->assertDontSee(
                'Draft Position'
            );
    }

    public function test_admin_can_delete_job_and_job_skills(): void
    {
        $this->login();

        $skill = Skill::create([
            'name' =>
                'Docker',

            'category' =>
                'DevOps',
        ]);

        $job = $this->createJob();

        $job->skills()->attach(
            $skill->id,
            [
                'importance' =>
                    'required',

                'weight' =>
                    90,

                'minimum_level' =>
                    'intermediate',
            ]
        );

        $response = $this->delete(
            route(
                'admin.jobs.destroy',
                $job
            )
        );

        $response->assertRedirect(
            route('admin.jobs.index')
        );

        $this->assertDatabaseMissing(
            'job_postings',
            [
                'id' =>
                    $job->id,
            ]
        );

        $this->assertDatabaseMissing(
            'job_skills',
            [
                'job_id' =>
                    $job->id,
            ]
        );

        /*
         * حذف Job لا يحذف Skill نفسها.
         */
        $this->assertDatabaseHas(
            'skills',
            [
                'id' =>
                    $skill->id,
            ]
        );
    }

    public function test_missing_job_returns_404(): void
    {
        $this->login();

        $response = $this->get(
            route(
                'admin.jobs.show',
                999999
            )
        );

        $response->assertNotFound();
    }
}