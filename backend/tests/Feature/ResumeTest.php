<?php


namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumeExperience;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResumeTest extends TestCase
{
    use RefreshDatabase;

    public function test_resume_factory_creates_resume_for_user(): void
    {
        $resume = Resume::factory()->create();

        $this->assertInstanceOf(User::class, $resume->user);
        $this->assertNotNull($resume->user_id);
    }

    public function test_resume_version_belongs_to_resume(): void
    {
        $resume = Resume::factory()->create();

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
        ]);

        $this->assertEquals($resume->id, $version->resume_id);
        $this->assertEquals($resume->id, $version->resume->id);
    }

    public function test_experience_belongs_to_resume_version(): void
    {
        $resume = Resume::factory()->create();

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
        ]);

        $experience = ResumeExperience::factory()->create([
            'resume_version_id' => $version->id,
        ]);

        $this->assertEquals(
            $version->id,
            $experience->resume_version_id
        );

        $this->assertEquals(
            $version->id,
            $experience->resumeVersion->id
        );
    }

    public function test_guest_cannot_access_resumes(): void
    {
        $response = $this->getJson('/api/resumes');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_only_see_own_resumes(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownResume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $otherResume = Resume::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/resumes');

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'id' => $ownResume->id,
        ]);

        $response->assertJsonMissing([
            'id' => $otherResume->id,
        ]);
    }

    public function test_user_cannot_view_another_users_resume(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherResume = Resume::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$otherResume->id}"
        );

        $response->assertForbidden();
    }

    public function test_user_cannot_update_another_users_resume(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherResume = Resume::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Original Resume',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson(
            "/api/resumes/{$otherResume->id}",
            [
                'title' => 'Hacked Resume',
            ]
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('resumes', [
            'id' => $otherResume->id,
            'title' => 'Original Resume',
        ]);
    }

    public function test_user_cannot_delete_another_users_resume(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherResume = Resume::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/resumes/{$otherResume->id}"
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('resumes', [
            'id' => $otherResume->id,
        ]);
    }
}

