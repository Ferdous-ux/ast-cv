<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumePublication;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResumePublicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_publication_to_own_resume(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        $version = ResumeVersion::factory()
            ->for($resume)
            ->create();

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/resumes/{$resume->id}/publications",
            [
                'title' => 'Artificial Intelligence Research',
                'publisher' => 'AST Research Center',
                'description' => 'Research publication about artificial intelligence.',
                'published_at' => '2026-01-15',
                'url' => 'https://example.com/publication',
                'sort_order' => 0,
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath(
                'data.title',
                'Artificial Intelligence Research'
            )
            ->assertJsonPath(
                'data.publisher',
                'AST Research Center'
            )
            ->assertJsonPath(
                'data.url',
                'https://example.com/publication'
            );

        $this->assertDatabaseHas('resume_publications', [
            'resume_version_id' => $version->id,
            'title' => 'Artificial Intelligence Research',
            'publisher' => 'AST Research Center',
        ]);
    }

    public function test_user_can_list_own_resume_publications(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        $version = ResumeVersion::factory()
            ->for($resume)
            ->create();

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        ResumePublication::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        ResumePublication::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/publications"
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_view_own_resume_publication(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        $version = ResumeVersion::factory()
            ->for($resume)
            ->create();

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $publication = ResumePublication::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/publications/{$publication->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $publication->id
            );
    }

    public function test_user_can_update_own_resume_publication(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        $version = ResumeVersion::factory()
            ->for($resume)
            ->create();

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $publication = ResumePublication::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson(
            "/api/resumes/{$resume->id}/publications/{$publication->id}",
            [
                'title' => 'Updated Research Publication',
                'publisher' => 'Updated Publisher',
                'url' => 'https://example.com/updated-publication',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.title',
                'Updated Research Publication'
            )
            ->assertJsonPath(
                'data.publisher',
                'Updated Publisher'
            )
            ->assertJsonPath(
                'data.url',
                'https://example.com/updated-publication'
            );

        $this->assertDatabaseHas('resume_publications', [
            'id' => $publication->id,
            'title' => 'Updated Research Publication',
            'publisher' => 'Updated Publisher',
        ]);
    }

    public function test_user_can_remove_own_resume_publication(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()
            ->for($user)
            ->create();

        $version = ResumeVersion::factory()
            ->for($resume)
            ->create();

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $publication = ResumePublication::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/resumes/{$resume->id}/publications/{$publication->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Publication removed successfully.',
            ]);

        $this->assertDatabaseMissing('resume_publications', [
            'id' => $publication->id,
        ]);
    }

    public function test_user_cannot_access_another_users_resume_publications(): void
    {
        $owner = User::factory()->create();

        $otherUser = User::factory()->create();

        $resume = Resume::factory()
            ->for($owner)
            ->create();

        $version = ResumeVersion::factory()
            ->for($resume)
            ->create();

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $publication = ResumePublication::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($otherUser);

        $this->getJson(
            "/api/resumes/{$resume->id}/publications"
        )->assertForbidden();

        $this->getJson(
            "/api/resumes/{$resume->id}/publications/{$publication->id}"
        )->assertForbidden();

        $this->putJson(
            "/api/resumes/{$resume->id}/publications/{$publication->id}",
            [
                'title' => 'Hacked Publication',
            ]
        )->assertForbidden();

        $this->deleteJson(
            "/api/resumes/{$resume->id}/publications/{$publication->id}"
        )->assertForbidden();
    }
}