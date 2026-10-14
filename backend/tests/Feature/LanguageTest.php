<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_resume_can_belong_to_a_language(): void
    {
        $user = User::factory()->create();

        $language = Language::create([
            'code' => 'ar',
            'name' => 'Arabic',
            'native_name' => 'العربية',
            'direction' => 'rtl',
            'is_active' => true,
        ]);

        $resume = Resume::create([
            'user_id' => $user->id,
            'language_id' => $language->id,
            'title' => 'Arabic CV',
            'status' => 'draft',
        ]);

        $this->assertTrue($resume->language->is($language));
        $this->assertSame('ar', $resume->language->code);
        $this->assertSame('rtl', $resume->language->direction);
    }

    public function test_language_can_have_many_resumes(): void
    {
        $user = User::factory()->create();

        $language = Language::create([
            'code' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_active' => true,
        ]);

        $resume1 = Resume::create([
            'user_id' => $user->id,
            'language_id' => $language->id,
            'title' => 'CV 1',
            'status' => 'draft',
        ]);

        $resume2 = Resume::create([
            'user_id' => $user->id,
            'language_id' => $language->id,
            'title' => 'CV 2',
            'status' => 'draft',
        ]);

        $this->assertCount(2, $language->resumes);
        $this->assertTrue(
            $language->resumes->contains($resume1)
        );
        $this->assertTrue(
            $language->resumes->contains($resume2)
        );
    }

    public function test_resume_version_can_have_spoken_languages(): void
    {
        $user = User::factory()->create();

        $resumeLanguage = Language::create([
            'code' => 'ar',
            'name' => 'Arabic',
            'native_name' => 'العربية',
            'direction' => 'rtl',
            'is_active' => true,
        ]);

        $spokenLanguage = Language::create([
            'code' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_active' => true,
        ]);

        $resume = Resume::create([
            'user_id' => $user->id,
            'language_id' => $resumeLanguage->id,
            'title' => 'My CV',
            'status' => 'draft',
        ]);

        $version = ResumeVersion::create([
            'resume_id' => $resume->id,
            'version_number' => 1,
            'status' => 'draft',
            'summary' => 'Professional CV',
            'template' => 'default',
        ]);

        $version->languages()->attach($spokenLanguage->id, [
            'proficiency' => 'intermediate',
            'sort_order' => 1,
        ]);

        $this->assertCount(1, $version->languages);
        $this->assertTrue(
            $version->languages->contains($spokenLanguage)
        );
        $this->assertSame(
            'intermediate',
            $version->languages->first()->pivot->proficiency
        );
    }
}