<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleware;
use App\Models\Resume;
use App\Models\ResumeTemplate;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * Disable only the custom admin authorization middleware.
         *
         * Important:
         * Do NOT use $this->withoutMiddleware() here, because that also
         * disables Laravel web middleware such as:
         * - ShareErrorsFromSession
         * - SubstituteBindings
         *
         * Those are required for Blade $errors and route model binding.
         */
        $this->withoutMiddleware(
            AdminMiddleware::class
        );

        $this->actingAs(
            User::factory()->create()
        );
    }

    public function test_admin_can_open_templates_index(): void
    {
        ResumeTemplate::create([
            'key' => 'default',
            'name' => 'Default',
            'description' => 'Default resume template.',
            'thumbnail' => null,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->get(
            route('admin.templates.index')
        );

        $response
            ->assertOk()
            ->assertViewIs('admin.templates.index')
            ->assertSee('Default');

        $this->assertDatabaseHas(
            'resume_templates',
            [
                'key' => 'default',
                'name' => 'Default',
            ]
        );
    }

    public function test_admin_can_open_create_template_page(): void
    {
        $response = $this->get(
            route('admin.templates.create')
        );

        $response
            ->assertOk()
            ->assertViewIs('admin.templates.create');
    }

    public function test_admin_can_create_template(): void
    {
        $response = $this->post(
            route('admin.templates.store'),
            [
                'key' => 'modern',
                'name' => 'Modern',
                'description' => 'Modern resume template.',
                'thumbnail' => 'images/templates/modern.png',
                'is_active' => 1,
                'sort_order' => 10,
            ]
        );

        $response->assertRedirect(
            route('admin.templates.index')
        );

        $this->assertDatabaseHas(
            'resume_templates',
            [
                'key' => 'modern',
                'name' => 'Modern',
                'description' => 'Modern resume template.',
                'thumbnail' => 'images/templates/modern.png',
                'is_active' => 1,
                'sort_order' => 10,
            ]
        );
    }

    public function test_template_key_must_be_unique(): void
    {
        ResumeTemplate::create([
            'key' => 'modern',
            'name' => 'Modern',
            'description' => null,
            'thumbnail' => null,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this
            ->from(route('admin.templates.create'))
            ->post(
                route('admin.templates.store'),
                [
                    'key' => 'modern',
                    'name' => 'Another Modern',
                    'description' => null,
                    'thumbnail' => null,
                    'is_active' => 1,
                    'sort_order' => 1,
                ]
            );

        $response
            ->assertRedirect(
                route('admin.templates.create')
            )
            ->assertSessionHasErrors('key');

        $this->assertSame(
            1,
            ResumeTemplate::where(
                'key',
                'modern'
            )->count()
        );
    }

    public function test_admin_can_update_template(): void
    {
        $template = ResumeTemplate::create([
            'key' => 'classic',
            'name' => 'Classic',
            'description' => 'Old description.',
            'thumbnail' => null,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $response = $this->put(
            route(
                'admin.templates.update',
                $template
            ),
            [
                'key' => 'classic',
                'name' => 'Classic Pro',
                'description' => 'Updated description.',
                'thumbnail' => 'images/templates/classic.png',
                'is_active' => 0,
                'sort_order' => 20,
            ]
        );

        $response->assertRedirect(
            route('admin.templates.index')
        );

        $this->assertDatabaseHas(
            'resume_templates',
            [
                'id' => $template->id,
                'key' => 'classic',
                'name' => 'Classic Pro',
                'description' => 'Updated description.',
                'thumbnail' => 'images/templates/classic.png',
                'is_active' => 0,
                'sort_order' => 20,
            ]
        );
    }

    public function test_admin_can_delete_unused_template(): void
    {
        $template = ResumeTemplate::create([
            'key' => 'unused',
            'name' => 'Unused Template',
            'description' => null,
            'thumbnail' => null,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->delete(
            route(
                'admin.templates.destroy',
                $template
            )
        );

        $response->assertRedirect(
            route('admin.templates.index')
        );

        $this->assertDatabaseMissing(
            'resume_templates',
            [
                'id' => $template->id,
            ]
        );
    }

    public function test_template_in_use_cannot_be_deleted(): void
    {
        $template = ResumeTemplate::create([
            'key' => 'default',
            'name' => 'Default',
            'description' => null,
            'thumbnail' => null,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $user = User::factory()->create();

        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => 'Test Resume',
            'status' => 'draft',
        ]);

        ResumeVersion::create([
            'resume_id' => $resume->id,
            'version_number' => 1,
            'status' => 'draft',
            'summary' => 'Test summary',
            'template' => 'default',
        ]);

        $response = $this->delete(
            route(
                'admin.templates.destroy',
                $template
            )
        );

        $response
            ->assertRedirect(
                route('admin.templates.index')
            )
            ->assertSessionHas('error');

        $this->assertDatabaseHas(
            'resume_templates',
            [
                'id' => $template->id,
                'key' => 'default',
            ]
        );
    }

    public function test_template_validation_rejects_invalid_data(): void
    {
        $response = $this
            ->from(route('admin.templates.create'))
            ->post(
                route('admin.templates.store'),
                [
                    'key' => 'invalid key with spaces',
                    'name' => '',
                    'description' => str_repeat('A', 3001),
                    'thumbnail' => str_repeat('B', 256),
                    'is_active' => 'invalid',
                    'sort_order' => -1,
                ]
            );

        $response
            ->assertRedirect(
                route('admin.templates.create')
            )
            ->assertSessionHasErrors([
                'key',
                'name',
                'description',
                'thumbnail',
                'is_active',
                'sort_order',
            ]);

        $this->assertDatabaseCount(
            'resume_templates',
            0
        );
    }
}
