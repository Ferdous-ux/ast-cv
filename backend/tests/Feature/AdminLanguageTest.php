<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleware;
use App\Models\Language;
use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLanguageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(
            AdminMiddleware::class
        );

        $this->actingAs(
            User::factory()->create()
        );
    }

    public function test_admin_can_open_languages_index(): void
    {
        Language::create([
            'code' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_active' => true,
        ]);

        $response = $this->get(
            route('admin.languages.index')
        );

        $response
            ->assertOk()
            ->assertViewIs('admin.languages.index')
            ->assertSee('English');
    }

    public function test_admin_can_open_create_language_page(): void
    {
        $this->get(
            route('admin.languages.create')
        )
            ->assertOk()
            ->assertViewIs('admin.languages.create');
    }

    public function test_admin_can_create_language(): void
    {
        $response = $this->post(
            route('admin.languages.store'),
            [
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'direction' => 'rtl',
                'is_active' => 1,
            ]
        );

        $response->assertRedirect(
            route('admin.languages.index')
        );

        $this->assertDatabaseHas(
            'languages',
            [
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'direction' => 'rtl',
                'is_active' => 1,
            ]
        );
    }

    public function test_language_code_and_name_must_be_unique(): void
    {
        Language::create([
            'code' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_active' => true,
        ]);

        $response = $this
            ->from(route('admin.languages.create'))
            ->post(
                route('admin.languages.store'),
                [
                    'code' => 'en',
                    'name' => 'English',
                    'native_name' => 'English',
                    'direction' => 'ltr',
                    'is_active' => 1,
                ]
            );

        $response
            ->assertRedirect(
                route('admin.languages.create')
            )
            ->assertSessionHasErrors([
                'code',
                'name',
            ]);
    }

    public function test_admin_can_update_language(): void
    {
        $language = Language::create([
            'code' => 'fr',
            'name' => 'French',
            'native_name' => 'Français',
            'direction' => 'ltr',
            'is_active' => true,
        ]);

        $response = $this->put(
            route(
                'admin.languages.update',
                $language
            ),
            [
                'code' => 'fr',
                'name' => 'French Language',
                'native_name' => 'Français',
                'direction' => 'ltr',
                'is_active' => 0,
            ]
        );

        $response->assertRedirect(
            route('admin.languages.index')
        );

        $this->assertDatabaseHas(
            'languages',
            [
                'id' => $language->id,
                'name' => 'French Language',
                'is_active' => 0,
            ]
        );
    }

    public function test_admin_can_delete_unused_language(): void
    {
        $language = Language::create([
            'code' => 'de',
            'name' => 'German',
            'native_name' => 'Deutsch',
            'direction' => 'ltr',
            'is_active' => true,
        ]);

        $response = $this->delete(
            route(
                'admin.languages.destroy',
                $language
            )
        );

        $response->assertRedirect(
            route('admin.languages.index')
        );

        $this->assertDatabaseMissing(
            'languages',
            [
                'id' => $language->id,
            ]
        );
    }

    public function test_writing_language_in_use_cannot_be_deleted(): void
    {
        $language = Language::create([
            'code' => 'ar',
            'name' => 'Arabic',
            'native_name' => 'العربية',
            'direction' => 'rtl',
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        Resume::create([
            'user_id' => $user->id,
            'language_id' => $language->id,
            'title' => 'Arabic Resume',
            'status' => 'draft',
        ]);

        $response = $this->delete(
            route(
                'admin.languages.destroy',
                $language
            )
        );

        $response
            ->assertRedirect(
                route('admin.languages.index')
            )
            ->assertSessionHas('error');

        $this->assertDatabaseHas(
            'languages',
            [
                'id' => $language->id,
            ]
        );
    }

    public function test_spoken_language_in_use_cannot_be_deleted(): void
    {
        $language = Language::create([
            'code' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => 'Developer Resume',
            'status' => 'draft',
        ]);

        $version = ResumeVersion::create([
            'resume_id' => $resume->id,
            'version_number' => 1,
            'status' => 'draft',
            'summary' => 'Summary',
            'template' => 'default',
        ]);

        $version->languages()->attach(
            $language->id,
            [
                'proficiency' => 'advanced',
                'sort_order' => 0,
            ]
        );

        $response = $this->delete(
            route(
                'admin.languages.destroy',
                $language
            )
        );

        $response
            ->assertRedirect(
                route('admin.languages.index')
            )
            ->assertSessionHas('error');

        $this->assertDatabaseHas(
            'languages',
            [
                'id' => $language->id,
            ]
        );
    }

    public function test_admin_can_search_and_filter_languages(): void
    {
        Language::create([
            'code' => 'ar',
            'name' => 'Arabic',
            'native_name' => 'العربية',
            'direction' => 'rtl',
            'is_active' => true,
        ]);

        Language::create([
            'code' => 'de',
            'name' => 'German',
            'native_name' => 'Deutsch',
            'direction' => 'ltr',
            'is_active' => false,
        ]);

        $response = $this->get(
            route(
                'admin.languages.index',
                [
                    'search' => 'Arabic',
                    'status' => 'active',
                ]
            )
        );

        $response
            ->assertOk()
            ->assertSee('Arabic')
            ->assertDontSee('German');
    }
}