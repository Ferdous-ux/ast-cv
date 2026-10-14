<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumeAward;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResumeAwardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_award_for_own_resume(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/resumes/{$resume->id}/awards",
            [
                'resume_version_id' => $version->id,
                'title' => 'Employee of the Year',
                'issuer' => 'ABC Company',
                'description' => 'Recognized for outstanding performance.',
                'awarded_at' => '2025-12-01',
                'url' => 'https://example.com/award',
                'sort_order' => 1,
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath(
                'data.title',
                'Employee of the Year'
            );

        $this->assertDatabaseHas('resume_awards', [
            'resume_version_id' => $version->id,
            'title' => 'Employee of the Year',
            'issuer' => 'ABC Company',
        ]);
    }

    public function test_user_can_list_own_resume_awards(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
        ]);

        ResumeAward::factory()->create([
            'resume_version_id' => $version->id,
            'title' => 'Best Developer Award',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/awards"
        );

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.title',
                'Best Developer Award'
            );
    }

    public function test_user_can_view_own_award(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
        ]);

        $award = ResumeAward::factory()->create([
            'resume_version_id' => $version->id,
            'title' => 'Innovation Award',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/awards/{$award->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.title',
                'Innovation Award'
            );
    }

    public function test_user_can_update_own_award(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
        ]);

        $award = ResumeAward::factory()->create([
            'resume_version_id' => $version->id,
            'title' => 'Old Award',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson(
            "/api/resumes/{$resume->id}/awards/{$award->id}",
            [
                'title' => 'Updated Award',
                'issuer' => 'Updated Organization',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.title',
                'Updated Award'
            );

        $this->assertDatabaseHas('resume_awards', [
            'id' => $award->id,
            'title' => 'Updated Award',
            'issuer' => 'Updated Organization',
        ]);
    }

    public function test_user_can_delete_own_award(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
        ]);

        $award = ResumeAward::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/resumes/{$resume->id}/awards/{$award->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Award deleted successfully.',
            ]);

        $this->assertDatabaseMissing('resume_awards', [
            'id' => $award->id,
        ]);
    }

    public function test_user_cannot_access_another_users_award(): void
    {
        $owner = User::factory()->create();

        $otherUser = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $owner->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
        ]);

        $award = ResumeAward::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        Sanctum::actingAs($otherUser);

        $this->getJson(
            "/api/resumes/{$resume->id}/awards"
        )->assertForbidden();

        $this->getJson(
            "/api/resumes/{$resume->id}/awards/{$award->id}"
        )->assertForbidden();

        $this->putJson(
            "/api/resumes/{$resume->id}/awards/{$award->id}",
            [
                'title' => 'Hacked Award',
            ]
        )->assertForbidden();

        $this->deleteJson(
            "/api/resumes/{$resume->id}/awards/{$award->id}"
        )->assertForbidden();
    }
}