<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumeSkill;
use App\Models\ResumeVersion;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResumeSkillTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_skill_to_own_resume(): void
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

        $skill = Skill::factory()->create([
            'name' => 'Laravel',
            'category' => 'Technical',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/resumes/{$resume->id}/skills",
            [
                'skill_id' => $skill->id,
                'level' => 'Advanced',
                'sort_order' => 1,
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath(
                'data.skill_id',
                $skill->id
            )
            ->assertJsonPath(
                'data.level',
                'Advanced'
            );

        $this->assertDatabaseHas('resume_skills', [
            'resume_version_id' => $version->id,
            'skill_id' => $skill->id,
            'level' => 'Advanced',
        ]);
    }

    public function test_user_can_list_own_resume_skills(): void
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

        $skill = Skill::factory()->create([
            'name' => 'PHP',
            'category' => 'Technical',
        ]);

        ResumeSkill::factory()->create([
            'resume_version_id' => $version->id,
            'skill_id' => $skill->id,
            'level' => 'Expert',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/skills"
        );

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.skill.name',
                'PHP'
            )
            ->assertJsonPath(
                'data.0.level',
                'Expert'
            );
    }

    public function test_user_can_view_own_resume_skill(): void
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

        $skill = Skill::factory()->create([
            'name' => 'Flutter',
        ]);

        $resumeSkill = ResumeSkill::factory()->create([
            'resume_version_id' => $version->id,
            'skill_id' => $skill->id,
            'level' => 'Advanced',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/resumes/{$resume->id}/skills/{$resumeSkill->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.skill.name',
                'Flutter'
            )
            ->assertJsonPath(
                'data.level',
                'Advanced'
            );
    }

    public function test_user_can_update_own_resume_skill(): void
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

        $skill = Skill::factory()->create([
            'name' => 'Laravel',
        ]);

        $resumeSkill = ResumeSkill::factory()->create([
            'resume_version_id' => $version->id,
            'skill_id' => $skill->id,
            'level' => 'Intermediate',
            'sort_order' => 1,
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson(
            "/api/resumes/{$resume->id}/skills/{$resumeSkill->id}",
            [
                'level' => 'Expert',
                'sort_order' => 2,
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.level',
                'Expert'
            )
            ->assertJsonPath(
                'data.sort_order',
                2
            );

        $this->assertDatabaseHas('resume_skills', [
            'id' => $resumeSkill->id,
            'level' => 'Expert',
            'sort_order' => 2,
        ]);
    }

    public function test_user_can_remove_own_resume_skill(): void
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

        $skill = Skill::factory()->create();

        $resumeSkill = ResumeSkill::factory()->create([
            'resume_version_id' => $version->id,
            'skill_id' => $skill->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/resumes/{$resume->id}/skills/{$resumeSkill->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Skill removed successfully.',
            ]);

        $this->assertDatabaseMissing('resume_skills', [
            'id' => $resumeSkill->id,
        ]);
    }

    public function test_user_cannot_access_another_users_resume_skills(): void
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

        $skill = Skill::factory()->create();

        $resumeSkill = ResumeSkill::factory()->create([
            'resume_version_id' => $version->id,
            'skill_id' => $skill->id,
        ]);

        Sanctum::actingAs($otherUser);

        $this->getJson(
            "/api/resumes/{$resume->id}/skills"
        )->assertForbidden();

        $this->getJson(
            "/api/resumes/{$resume->id}/skills/{$resumeSkill->id}"
        )->assertForbidden();

        $this->putJson(
            "/api/resumes/{$resume->id}/skills/{$resumeSkill->id}",
            [
                'level' => 'Expert',
            ]
        )->assertForbidden();

        $this->deleteJson(
            "/api/resumes/{$resume->id}/skills/{$resumeSkill->id}"
        )->assertForbidden();
    }
}