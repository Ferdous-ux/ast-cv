<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumeExperience;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_experience(): void
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
            ->postJson("/api/resumes/{$resume->id}/experiences", [
                'resume_version_id' => $version->id,
                'company_name' => 'AST Company',
                'job_title' => 'Software Developer',
                'location' => 'Sanaa, Yemen',
                'start_date' => '2025-01-01',
                'is_current' => true,
                'description' => 'Developing software applications.',
                'sort_order' => 1,
            ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.company_name', 'AST Company')
            ->assertJsonPath('data.job_title', 'Software Developer');

        $this->assertDatabaseHas('resume_experiences', [
            'resume_version_id' => $version->id,
            'company_name' => 'AST Company',
            'job_title' => 'Software Developer',
        ]);
    }

    public function test_authenticated_user_can_list_experiences(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        ResumeExperience::create([
            'resume_version_id' => $version->id,
            'company_name' => 'AST Company',
            'job_title' => 'Developer',
            'start_date' => '2025-01-01',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/resumes/{$resume->id}/experiences");

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_authenticated_user_can_view_experience(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $experience = ResumeExperience::create([
            'resume_version_id' => $version->id,
            'company_name' => 'AST Company',
            'job_title' => 'Developer',
            'start_date' => '2025-01-01',
        ]);

        $response = $this->actingAs($user)
            ->getJson(
                "/api/resumes/{$resume->id}/experiences/{$experience->id}"
            );

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $experience->id);
    }

    public function test_authenticated_user_can_update_experience(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $experience = ResumeExperience::create([
            'resume_version_id' => $version->id,
            'company_name' => 'Old Company',
            'job_title' => 'Developer',
            'start_date' => '2025-01-01',
        ]);

        $response = $this->actingAs($user)
            ->putJson(
                "/api/resumes/{$resume->id}/experiences/{$experience->id}",
                [
                    'company_name' => 'New Company',
                    'job_title' => 'Senior Developer',
                ]
            );

        $response
            ->assertOk()
            ->assertJsonPath('data.company_name', 'New Company')
            ->assertJsonPath('data.job_title', 'Senior Developer');
    }

    public function test_authenticated_user_can_delete_experience(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $experience = ResumeExperience::create([
            'resume_version_id' => $version->id,
            'company_name' => 'AST Company',
            'job_title' => 'Developer',
            'start_date' => '2025-01-01',
        ]);

        $response = $this->actingAs($user)
            ->deleteJson(
                "/api/resumes/{$resume->id}/experiences/{$experience->id}"
            );

        $response->assertOk();

        $this->assertDatabaseMissing('resume_experiences', [
            'id' => $experience->id,
        ]);
    }

    public function test_user_cannot_access_another_users_experience(): void
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

        $experience = ResumeExperience::create([
            'resume_version_id' => $version->id,
            'company_name' => 'Private Company',
            'job_title' => 'Developer',
            'start_date' => '2025-01-01',
        ]);

        $response = $this->actingAs($user)
            ->getJson(
                "/api/resumes/{$otherResume->id}/experiences/{$experience->id}"
            );

        $response->assertForbidden();
    }
}