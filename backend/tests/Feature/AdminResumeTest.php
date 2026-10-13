<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleware;
use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResumeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * نعطل Middleware الخاص بصلاحية الأدمن فقط.
         * لا نعطل جميع Middleware حتى تبقى:
         * - Session
         * - Route Model Binding
         * - Blade errors
         * تعمل بشكل طبيعي.
         */
        $this->withoutMiddleware(
            AdminMiddleware::class
        );

        $this->actingAs(
            User::factory()->create()
        );
    }


    public function test_admin_can_open_resumes_index(): void
    {
        $user = User::factory()->create();

        Resume::create([
            'user_id' => $user->id,
            'title' => 'Backend Developer Resume',
            'status' => 'draft',
        ]);

        $response = $this->get(
            route('admin.resumes.index')
        );

        $response
            ->assertOk()
            ->assertViewIs('admin.resumes.index')
            ->assertSee('Backend Developer Resume');

        $this->assertDatabaseHas(
            'resumes',
            [
                'title' => 'Backend Developer Resume',
                'status' => 'draft',
            ]
        );
    }


    public function test_admin_can_search_resumes_by_title(): void
    {
        $user = User::factory()->create();

        Resume::create([
            'user_id' => $user->id,
            'title' => 'Laravel Developer Resume',
            'status' => 'draft',
        ]);

        Resume::create([
            'user_id' => $user->id,
            'title' => 'Graphic Designer Resume',
            'status' => 'draft',
        ]);

        $response = $this->get(
            route(
                'admin.resumes.index',
                [
                    'search' => 'Laravel',
                ]
            )
        );

        $response
            ->assertOk()
            ->assertSee('Laravel Developer Resume')
            ->assertDontSee('Graphic Designer Resume');
    }


    public function test_admin_can_search_resumes_by_user_name(): void
    {
        $firstUser = User::factory()->create([
            'name' => 'Ahmed Developer',
        ]);

        $secondUser = User::factory()->create([
            'name' => 'Mohammed Designer',
        ]);

        Resume::create([
            'user_id' => $firstUser->id,
            'title' => 'Ahmed Resume',
            'status' => 'draft',
        ]);

        Resume::create([
            'user_id' => $secondUser->id,
            'title' => 'Mohammed Resume',
            'status' => 'draft',
        ]);

        $response = $this->get(
            route(
                'admin.resumes.index',
                [
                    'search' => 'Ahmed Developer',
                ]
            )
        );

        $response
            ->assertOk()
            ->assertSee('Ahmed Resume')
            ->assertDontSee('Mohammed Resume');
    }


    public function test_admin_can_filter_resumes_by_status(): void
    {
        $user = User::factory()->create();

        Resume::create([
            'user_id' => $user->id,
            'title' => 'Active Resume',
            'status' => 'active',
        ]);

        Resume::create([
            'user_id' => $user->id,
            'title' => 'Draft Resume',
            'status' => 'draft',
        ]);

        $response = $this->get(
            route(
                'admin.resumes.index',
                [
                    'status' => 'active',
                ]
            )
        );

        $response
            ->assertOk()
            ->assertSee('Active Resume')
            ->assertDontSee('Draft Resume');
    }


    public function test_admin_can_view_resume_details(): void
    {
        $user = User::factory()->create([
            'name' => 'Resume Owner',
            'email' => 'resume-owner@example.com',
        ]);

        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => 'Senior Developer Resume',
            'status' => 'active',
        ]);

        $version = ResumeVersion::create([
            'resume_id' => $resume->id,
            'version_number' => 1,
            'status' => 'active',
            'summary' => 'Experienced Laravel developer.',
            'template' => 'default',
        ]);

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $response = $this->get(
            route(
                'admin.resumes.show',
                $resume
            )
        );

        $response
            ->assertOk()
            ->assertViewIs('admin.resumes.show')
            ->assertSee('Senior Developer Resume')
            ->assertSee('Resume Owner')
            ->assertSee('resume-owner@example.com')
            ->assertSee('Experienced Laravel developer.')
            ->assertSee('v1');
    }


    public function test_index_counts_resume_versions(): void
    {
        $user = User::factory()->create();

        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => 'Versioned Resume',
            'status' => 'draft',
        ]);

        ResumeVersion::create([
            'resume_id' => $resume->id,
            'version_number' => 1,
            'status' => 'draft',
            'summary' => 'First version',
            'template' => 'default',
        ]);

        ResumeVersion::create([
            'resume_id' => $resume->id,
            'version_number' => 2,
            'status' => 'draft',
            'summary' => 'Second version',
            'template' => 'default',
        ]);

        $response = $this->get(
            route('admin.resumes.index')
        );

        $response->assertOk();

        $response->assertViewHas(
            'totalVersions',
            2
        );
    }


    public function test_show_returns_404_for_missing_resume(): void
    {
        $response = $this->get(
            '/admin/resumes/999999'
        );

        $response->assertNotFound();
    }
}