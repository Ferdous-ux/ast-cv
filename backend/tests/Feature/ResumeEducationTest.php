<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumeEducation;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeEducationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_education(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/resumes/{$resume->id}/educations", [
                'resume_version_id' => $version->id,
                'institution' => 'Sanaa University',
                'degree' => 'Bachelor',
                'field_of_study' => 'Computer Science',
                'location' => 'Sanaa, Yemen',
                'start_date' => '2021-09-01',
                'end_date' => '2025-06-01',
                'description' => 'Bachelor degree in Computer Science.',
                'sort_order' => 1,
            ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.institution', 'Sanaa University')
            ->assertJsonPath('data.degree', 'Bachelor');

        $this->assertDatabaseHas('resume_educations', [
            'resume_version_id' => $version->id,
            'institution' => 'Sanaa University',
            'degree' => 'Bachelor',
        ]);
    }

    public function test_authenticated_user_can_list_educations(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        ResumeEducation::create([
            'resume_version_id' => $version->id,
            'institution' => 'Sanaa University',
            'degree' => 'Bachelor',
            'field_of_study' => 'Computer Science',
            'start_date' => '2021-09-01',
            'end_date' => '2025-06-01',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/resumes/{$resume->id}/educations");

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_authenticated_user_can_view_education(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $education = ResumeEducation::create([
            'resume_version_id' => $version->id,
            'institution' => 'Sanaa University',
            'degree' => 'Bachelor',
            'field_of_study' => 'Computer Science',
            'start_date' => '2021-09-01',
            'end_date' => '2025-06-01',
        ]);

        $response = $this->actingAs($user)
            ->getJson(
                "/api/resumes/{$resume->id}/educations/{$education->id}"
            );

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $education->id);
    }

    public function test_authenticated_user_can_update_education(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $education = ResumeEducation::create([
            'resume_version_id' => $version->id,
            'institution' => 'Old University',
            'degree' => 'Bachelor',
            'field_of_study' => 'Computer Science',
            'start_date' => '2021-09-01',
            'end_date' => '2025-06-01',
        ]);

        $response = $this->actingAs($user)
            ->putJson(
                "/api/resumes/{$resume->id}/educations/{$education->id}",
                [
                    'institution' => 'New University',
                    'degree' => 'Master',
                    'field_of_study' => 'Software Engineering',
                ]
            );

        $response
            ->assertOk()
            ->assertJsonPath('data.institution', 'New University')
            ->assertJsonPath('data.degree', 'Master')
            ->assertJsonPath(
                'data.field_of_study',
                'Software Engineering'
            );
    }

    public function test_authenticated_user_can_delete_education(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $education = ResumeEducation::create([
            'resume_version_id' => $version->id,
            'institution' => 'Sanaa University',
            'degree' => 'Bachelor',
            'start_date' => '2021-09-01',
            'end_date' => '2025-06-01',
        ]);

        $response = $this->actingAs($user)
            ->deleteJson(
                "/api/resumes/{$resume->id}/educations/{$education->id}"
            );

        $response->assertOk();

        $this->assertDatabaseMissing('resume_educations', [
            'id' => $education->id,
        ]);
    }

    public function test_user_cannot_access_another_users_education(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherResume = Resume::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $otherResume->id,
            'version_number' => 1,
        ]);

        $education = ResumeEducation::create([
            'resume_version_id' => $version->id,
            'institution' => 'Private University',
            'degree' => 'Bachelor',
            'start_date' => '2021-09-01',
            'end_date' => '2025-06-01',
        ]);

        $response = $this->actingAs($user)
            ->getJson(
                "/api/resumes/{$otherResume->id}/educations/{$education->id}"
            );

        $response->assertForbidden();
    }
}