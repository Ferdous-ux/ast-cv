<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResumeVersionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_version_for_own_resume(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/resumes/{$resume->id}/versions",
            [
                'summary' => 'Professional software developer.',
                'template' => 'modern',
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath(
                'data.version_number',
                1
            )
            ->assertJsonPath(
                'data.status',
                'active'
            )
            ->assertJsonPath(
                'data.summary',
                'Professional software developer.'
            )
            ->assertJsonPath(
                'data.template',
                'modern'
            );

        $versionId = $response->json('data.id');

        $this->assertDatabaseHas('resume_versions', [
            'id' => $versionId,
            'resume_id' => $resume->id,
            'version_number' => 1,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('resumes', [
            'id' => $resume->id,
            'current_version_id' => $versionId,
        ]);
    }

    public function test_new_versions_are_numbered_sequentially_and_become_current(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        Sanctum::actingAs($user);

        $firstResponse = $this->postJson(
            "/api/resumes/{$resume->id}/versions",
            [
                'summary' => 'Version one',
                'template' => 'classic',
            ]
        );

        $firstResponse->assertStatus(201);

        $firstVersionId = $firstResponse->json('data.id');

        $secondResponse = $this->postJson(
            "/api/resumes/{$resume->id}/versions",
            [
                'summary' => 'Version two',
                'template' => 'modern',
            ]
        );

        $secondResponse
            ->assertStatus(201)
            ->assertJsonPath(
                'data.version_number',
                2
            )
            ->assertJsonPath(
                'data.status',
                'active'
            );

        $secondVersionId = $secondResponse->json('data.id');

        $this->assertDatabaseHas('resume_versions', [
            'id' => $firstVersionId,
            'resume_id' => $resume->id,
            'version_number' => 1,
            'status' => 'archived',
        ]);

        $this->assertDatabaseHas('resume_versions', [
            'id' => $secondVersionId,
            'resume_id' => $resume->id,
            'version_number' => 2,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('resumes', [
            'id' => $resume->id,
            'current_version_id' => $secondVersionId,
        ]);
    }

    public function test_user_can_list_own_resume_versions(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        ResumeVersion::factory()
            ->for($resume)
            ->create([
                'version_number' => 1,
            ]);

        ResumeVersion::factory()
            ->for($resume)
            ->create([
                'version_number' => 2,
            ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/versions"
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_view_own_resume_version(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        $version = ResumeVersion::factory()
            ->for($resume)
            ->create();

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/versions/{$version->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $version->id
            );
    }

    public function test_user_can_update_own_resume_version(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        $version = ResumeVersion::factory()
            ->for($resume)
            ->create();

        Sanctum::actingAs($user);

        $response = $this->putJson(
            "/api/resumes/{$resume->id}/versions/{$version->id}",
            [
                'summary' => 'Updated professional summary.',
                'template' => 'modern',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.summary',
                'Updated professional summary.'
            )
            ->assertJsonPath(
                'data.template',
                'modern'
            );

        $this->assertDatabaseHas('resume_versions', [
            'id' => $version->id,
            'summary' => 'Updated professional summary.',
            'template' => 'modern',
        ]);
    }

    public function test_user_can_activate_another_resume_version(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        $firstVersion = ResumeVersion::factory()
            ->for($resume)
            ->create([
                'version_number' => 1,
                'status' => 'active',
            ]);

        $secondVersion = ResumeVersion::factory()
            ->for($resume)
            ->create([
                'version_number' => 2,
                'status' => 'archived',
            ]);

        $resume->update([
            'current_version_id' => $firstVersion->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/resumes/{$resume->id}/versions/{$secondVersion->id}/activate"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $secondVersion->id
            )
            ->assertJsonPath(
                'data.status',
                'active'
            );

        $this->assertDatabaseHas('resume_versions', [
            'id' => $firstVersion->id,
            'status' => 'archived',
        ]);

        $this->assertDatabaseHas('resume_versions', [
            'id' => $secondVersion->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('resumes', [
            'id' => $resume->id,
            'current_version_id' => $secondVersion->id,
        ]);
    }

    public function test_user_cannot_delete_last_resume_version(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        $version = ResumeVersion::factory()
            ->for($resume)
            ->create([
                'version_number' => 1,
            ]);

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/resumes/{$resume->id}/versions/{$version->id}"
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'A resume must have at least one version.',
            ]);

        $this->assertDatabaseHas('resume_versions', [
            'id' => $version->id,
        ]);
    }

    public function test_user_can_delete_non_current_resume_version(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        $firstVersion = ResumeVersion::factory()
            ->for($resume)
            ->create([
                'version_number' => 1,
                'status' => 'archived',
            ]);

        $secondVersion = ResumeVersion::factory()
            ->for($resume)
            ->create([
                'version_number' => 2,
                'status' => 'active',
            ]);

        $resume->update([
            'current_version_id' => $secondVersion->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/resumes/{$resume->id}/versions/{$firstVersion->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Resume version deleted successfully.',
            ]);

        $this->assertDatabaseMissing('resume_versions', [
            'id' => $firstVersion->id,
        ]);

        $this->assertDatabaseHas('resume_versions', [
            'id' => $secondVersion->id,
        ]);

        $this->assertDatabaseHas('resumes', [
            'id' => $resume->id,
            'current_version_id' => $secondVersion->id,
        ]);
    }

    public function test_deleting_current_version_selects_new_current_version(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        $firstVersion = ResumeVersion::factory()
            ->for($resume)
            ->create([
                'version_number' => 1,
                'status' => 'active',
            ]);

        $secondVersion = ResumeVersion::factory()
            ->for($resume)
            ->create([
                'version_number' => 2,
                'status' => 'active',
            ]);

        $resume->update([
            'current_version_id' => $secondVersion->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/resumes/{$resume->id}/versions/{$secondVersion->id}"
        );

        $response->assertOk();

        $this->assertDatabaseMissing('resume_versions', [
            'id' => $secondVersion->id,
        ]);

        $this->assertDatabaseHas('resume_versions', [
            'id' => $firstVersion->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('resumes', [
            'id' => $resume->id,
            'current_version_id' => $firstVersion->id,
        ]);
    }

    public function test_user_cannot_access_another_users_resume_versions(): void
    {
        $owner = User::factory()->create();

        $otherUser = User::factory()->create();

        $resume = Resume::factory()
            ->for($owner)
            ->create();

        $version = ResumeVersion::factory()
            ->for($resume)
            ->create();

        Sanctum::actingAs($otherUser);

        $this->getJson(
            "/api/resumes/{$resume->id}/versions"
        )->assertForbidden();

        $this->getJson(
            "/api/resumes/{$resume->id}/versions/{$version->id}"
        )->assertForbidden();

        $this->putJson(
            "/api/resumes/{$resume->id}/versions/{$version->id}",
            [
                'summary' => 'Hacked',
            ]
        )->assertForbidden();

        $this->postJson(
            "/api/resumes/{$resume->id}/versions/{$version->id}/activate"
        )->assertForbidden();

        $this->deleteJson(
            "/api/resumes/{$resume->id}/versions/{$version->id}"
        )->assertForbidden();
    }

    public function test_version_from_another_resume_cannot_be_accessed(): void
    {
        $user = User::factory()->create();

        $firstResume = Resume::factory()
            ->for($user)
            ->create();

        $secondResume = Resume::factory()
            ->for($user)
            ->create();

        $version = ResumeVersion::factory()
            ->for($secondResume)
            ->create();

        Sanctum::actingAs($user);

        $this->getJson(
            "/api/resumes/{$firstResume->id}/versions/{$version->id}"
        )->assertNotFound();

        $this->putJson(
            "/api/resumes/{$firstResume->id}/versions/{$version->id}",
            [
                'summary' => 'Invalid access',
            ]
        )->assertNotFound();

        $this->postJson(
            "/api/resumes/{$firstResume->id}/versions/{$version->id}/activate"
        )->assertNotFound();

        $this->deleteJson(
            "/api/resumes/{$firstResume->id}/versions/{$version->id}"
        )->assertNotFound();
    }
}