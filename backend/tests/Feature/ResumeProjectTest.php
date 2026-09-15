<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumeProject;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_project(): void
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
            ->postJson("/api/resumes/{$resume->id}/projects", [
                'resume_version_id' => $version->id,
                'name' => 'AST-CV',
                'description' => 'ATS-oriented CV builder platform.',
                'project_url' => 'https://example.com',
                'repository_url' => 'https://github.com/example/ast-cv',
                'start_date' => '2026-01-01',
                'end_date' => '2026-06-01',
                'sort_order' => 1,
            ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'AST-CV')
            ->assertJsonPath(
                'data.description',
                'ATS-oriented CV builder platform.'
            )
            ->assertJsonPath(
                'data.project_url',
                'https://example.com'
            )
            ->assertJsonPath(
                'data.repository_url',
                'https://github.com/example/ast-cv'
            );

        $this->assertDatabaseHas('resume_projects', [
            'resume_version_id' => $version->id,
            'name' => 'AST-CV',
        ]);
    }

    public function test_authenticated_user_can_list_projects(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        ResumeProject::create([
            'resume_version_id' => $version->id,
            'name' => 'AST-CV',
            'description' => 'CV builder platform.',
            'project_url' => 'https://example.com',
            'repository_url' => 'https://github.com/example/ast-cv',
            'start_date' => '2026-01-01',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/resumes/{$resume->id}/projects");

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_authenticated_user_can_view_project(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $project = ResumeProject::create([
            'resume_version_id' => $version->id,
            'name' => 'AST-CV',
            'description' => 'CV builder platform.',
            'project_url' => 'https://example.com',
            'repository_url' => 'https://github.com/example/ast-cv',
            'start_date' => '2026-01-01',
        ]);

        $response = $this->actingAs($user)
            ->getJson(
                "/api/resumes/{$resume->id}/projects/{$project->id}"
            );

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $project->id)
            ->assertJsonPath('data.name', 'AST-CV');
    }

    public function test_authenticated_user_can_update_project(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $project = ResumeProject::create([
            'resume_version_id' => $version->id,
            'name' => 'Old Project',
            'description' => 'Old description.',
            'project_url' => 'https://old-example.com',
            'repository_url' => 'https://github.com/example/old',
            'start_date' => '2026-01-01',
        ]);

        $response = $this->actingAs($user)
            ->putJson(
                "/api/resumes/{$resume->id}/projects/{$project->id}",
                [
                    'name' => 'New Project',
                    'description' => 'Updated description.',
                    'project_url' => 'https://new-example.com',
                    'repository_url' => 'https://github.com/example/new',
                ]
            );

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'New Project')
            ->assertJsonPath(
                'data.description',
                'Updated description.'
            )
            ->assertJsonPath(
                'data.project_url',
                'https://new-example.com'
            )
            ->assertJsonPath(
                'data.repository_url',
                'https://github.com/example/new'
            );
    }

    public function test_authenticated_user_can_delete_project(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $project = ResumeProject::create([
            'resume_version_id' => $version->id,
            'name' => 'AST-CV',
            'description' => 'CV builder platform.',
            'start_date' => '2026-01-01',
        ]);

        $response = $this->actingAs($user)
            ->deleteJson(
                "/api/resumes/{$resume->id}/projects/{$project->id}"
            );

        $response->assertOk();

        $this->assertDatabaseMissing('resume_projects', [
            'id' => $project->id,
        ]);
    }

    public function test_user_cannot_access_another_users_project(): void
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

        $project = ResumeProject::create([
            'resume_version_id' => $version->id,
            'name' => 'Private Project',
            'description' => 'Private project.',
            'start_date' => '2026-01-01',
        ]);

        $response = $this->actingAs($user)
            ->getJson(
                "/api/resumes/{$otherResume->id}/projects/{$project->id}"
            );

        $response->assertForbidden();
    }
}