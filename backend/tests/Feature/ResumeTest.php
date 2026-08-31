<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumeExperience;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}