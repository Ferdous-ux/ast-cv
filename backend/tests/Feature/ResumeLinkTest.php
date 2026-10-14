<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumeLink;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResumeLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_link_to_own_resume(): void
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
            "/api/resumes/{$resume->id}/links",
            [
                'label' => 'GitHub',
                'url' => 'https://github.com/example',
                'sort_order' => 0,
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath(
                'data.label',
                'GitHub'
            )
            ->assertJsonPath(
                'data.url',
                'https://github.com/example'
            );

        $this->assertDatabaseHas('resume_links', [
            'resume_version_id' => $version->id,
            'label' => 'GitHub',
            'url' => 'https://github.com/example',
        ]);
    }

    public function test_user_can_list_own_resume_links(): void
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

        ResumeLink::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        ResumeLink::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/links"
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_view_own_resume_link(): void
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

        $link = ResumeLink::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/links/{$link->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $link->id
            );
    }

    public function test_user_can_update_own_resume_link(): void
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

        $link = ResumeLink::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson(
            "/api/resumes/{$resume->id}/links/{$link->id}",
            [
                'label' => 'Updated GitHub',
                'url' => 'https://github.com/updated',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.label',
                'Updated GitHub'
            )
            ->assertJsonPath(
                'data.url',
                'https://github.com/updated'
            );

        $this->assertDatabaseHas('resume_links', [
            'id' => $link->id,
            'label' => 'Updated GitHub',
            'url' => 'https://github.com/updated',
        ]);
    }

    public function test_user_can_remove_own_resume_link(): void
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

        $link = ResumeLink::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/resumes/{$resume->id}/links/{$link->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Link removed successfully.',
            ]);

        $this->assertDatabaseMissing('resume_links', [
            'id' => $link->id,
        ]);
    }

    public function test_user_cannot_access_another_users_resume_links(): void
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

        $link = ResumeLink::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($otherUser);

        $this->getJson(
            "/api/resumes/{$resume->id}/links"
        )->assertForbidden();

        $this->getJson(
            "/api/resumes/{$resume->id}/links/{$link->id}"
        )->assertForbidden();

        $this->putJson(
            "/api/resumes/{$resume->id}/links/{$link->id}",
            [
                'label' => 'Hacked Link',
            ]
        )->assertForbidden();

        $this->deleteJson(
            "/api/resumes/{$resume->id}/links/{$link->id}"
        )->assertForbidden();
    }
}