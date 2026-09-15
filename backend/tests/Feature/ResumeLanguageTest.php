<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Resume;
use App\Models\ResumeLanguage;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResumeLanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_language_to_own_resume(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'status' => 'active',
        ]);

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $language = Language::factory()->create([
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en',
            'direction' => 'ltr',
            'is_active' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/resumes/{$resume->id}/languages",
            [
                'language_id' => $language->id,
                'proficiency' => 'Advanced',
                'sort_order' => 1,
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath(
                'data.language_id',
                $language->id
            )
            ->assertJsonPath(
                'data.proficiency',
                'Advanced'
            );

        $this->assertDatabaseHas('resume_languages', [
            'resume_version_id' => $version->id,
            'language_id' => $language->id,
            'proficiency' => 'Advanced',
        ]);
    }

    public function test_user_can_list_own_resume_languages(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'status' => 'active',
        ]);

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $language = Language::factory()->create([
            'name' => 'Arabic',
            'native_name' => 'العربية',
            'code' => 'ar',
            'direction' => 'rtl',
            'is_active' => true,
        ]);

        ResumeLanguage::factory()->create([
            'resume_version_id' => $version->id,
            'language_id' => $language->id,
            'proficiency' => 'Native',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/languages"
        );

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.language.name',
                'Arabic'
            )
            ->assertJsonPath(
                'data.0.proficiency',
                'Native'
            );
    }

    public function test_user_can_view_own_resume_language(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'status' => 'active',
        ]);

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $language = Language::factory()->create([
            'name' => 'French',
            'code' => 'fr',
        ]);

        $resumeLanguage = ResumeLanguage::factory()->create([
            'resume_version_id' => $version->id,
            'language_id' => $language->id,
            'proficiency' => 'Intermediate',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/languages/{$resumeLanguage->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.language.name',
                'French'
            )
            ->assertJsonPath(
                'data.proficiency',
                'Intermediate'
            );
    }

    public function test_user_can_update_own_resume_language(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'status' => 'active',
        ]);

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $language = Language::factory()->create([
            'name' => 'English',
            'code' => 'en',
        ]);

        $resumeLanguage = ResumeLanguage::factory()->create([
            'resume_version_id' => $version->id,
            'language_id' => $language->id,
            'proficiency' => 'Intermediate',
            'sort_order' => 1,
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson(
            "/api/resumes/{$resume->id}/languages/{$resumeLanguage->id}",
            [
                'proficiency' => 'Native',
                'sort_order' => 2,
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.proficiency',
                'Native'
            )
            ->assertJsonPath(
                'data.sort_order',
                2
            );

        $this->assertDatabaseHas('resume_languages', [
            'id' => $resumeLanguage->id,
            'proficiency' => 'Native',
            'sort_order' => 2,
        ]);
    }

    public function test_user_can_remove_own_resume_language(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'status' => 'active',
        ]);

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $language = Language::factory()->create();

        $resumeLanguage = ResumeLanguage::factory()->create([
            'resume_version_id' => $version->id,
            'language_id' => $language->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/resumes/{$resume->id}/languages/{$resumeLanguage->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Language removed successfully.',
            ]);

        $this->assertDatabaseMissing('resume_languages', [
            'id' => $resumeLanguage->id,
        ]);
    }

    public function test_user_cannot_access_another_users_resume_languages(): void
    {
        $owner = User::factory()->create();

        $otherUser = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $owner->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'status' => 'active',
        ]);

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $language = Language::factory()->create();

        $resumeLanguage = ResumeLanguage::factory()->create([
            'resume_version_id' => $version->id,
            'language_id' => $language->id,
        ]);

        Sanctum::actingAs($otherUser);

        $this->getJson(
            "/api/resumes/{$resume->id}/languages"
        )->assertForbidden();

        $this->getJson(
            "/api/resumes/{$resume->id}/languages/{$resumeLanguage->id}"
        )->assertForbidden();

        $this->putJson(
            "/api/resumes/{$resume->id}/languages/{$resumeLanguage->id}",
            [
                'proficiency' => 'Native',
            ]
        )->assertForbidden();

        $this->deleteJson(
            "/api/resumes/{$resume->id}/languages/{$resumeLanguage->id}"
        )->assertForbidden();
    }
}