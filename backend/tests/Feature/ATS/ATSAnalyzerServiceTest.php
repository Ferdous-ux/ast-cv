<?php

namespace Tests\Feature\ATS;

use App\Models\Resume;
use App\Models\ResumeEducation;
use App\Models\ResumeExperience;
use App\Models\ResumeVersion;
use App\Models\Skill;
use App\Models\SkillAlias;
use App\Models\User;
use App\Modules\ATS\Models\ATSAnalysis;
use App\Modules\ATS\Services\ATSAnalyzerService;
use App\Modules\Jobs\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ATSAnalyzerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_and_preferred_skill_weights_are_calculated_correctly(): void
    {
        [$resumeVersion, $job] = $this->makeResumeAndJob();

        $php = $this->makeSkill('PHP', 'Backend');
        $git = $this->makeSkill('Git', 'Tools');

        $this->attachJobSkill(
            $job,
            $php,
            'required',
            100,
            'intermediate'
        );

        $this->attachJobSkill(
            $job,
            $git,
            'preferred',
            60,
            'beginner'
        );

        $this->attachResumeSkill(
            $resumeVersion,
            $php,
            'intermediate'
        );

        $analysis = $this->analyze(
            $resumeVersion,
            $job
        );

        $this->assertSame(
            76.92,
            (float) $analysis->skills_score
        );

        $this->assertSame(
            130.0,
            (float) $analysis->details['skills_total_weight']
        );

        $this->assertSame(
            100.0,
            (float) $analysis->details['skills_earned_weight']
        );

        $this->assertSame(
            1,
            $analysis->details['skills_required']
        );

        $this->assertSame(
            1,
            $analysis->details['skills_preferred']
        );

        $this->assertSame(
            ['PHP'],
            collect($analysis->matched_skills)
                ->pluck('name')
                ->values()
                ->all()
        );

        $this->assertSame(
            ['Git'],
            collect($analysis->missing_skills)
                ->pluck('name')
                ->values()
                ->all()
        );
    }

    public function test_skill_level_affects_earned_weight(): void
    {
        [$resumeVersion, $job] = $this->makeResumeAndJob();

        $laravel = $this->makeSkill(
            'Laravel',
            'Backend'
        );

        $this->attachJobSkill(
            $job,
            $laravel,
            'required',
            100,
            'advanced'
        );

        $this->attachResumeSkill(
            $resumeVersion,
            $laravel,
            'intermediate'
        );

        $analysis = $this->analyze(
            $resumeVersion,
            $job
        );

        $this->assertSame(
            65.0,
            (float) $analysis->skills_score
        );

        $matched =
            collect($analysis->matched_skills)
                ->firstWhere(
                    'name',
                    'Laravel'
                );

        $this->assertNotNull($matched);

        $this->assertSame(
            0.65,
            (float) $matched['level_factor']
        );

        $this->assertSame(
            65.0,
            (float) $matched['earned_weight']
        );

        $this->assertFalse(
            $matched['meets_minimum_level']
        );
    }

    public function test_job_skill_can_be_detected_from_alias_in_job_text(): void
    {
        [$resumeVersion, $job] =
            $this->makeResumeAndJob([
                'description' =>
                    'Experience with Amazon Web Services is preferred.',
            ]);

        $aws = $this->makeSkill(
            'AWS',
            'Cloud'
        );

        $this->makeAlias(
            $aws,
            'Amazon Web Services'
        );

        $analysis = $this->analyze(
            $resumeVersion,
            $job
        );

        $this->assertSame(
            'ATS-3.1',
            $analysis->details['engine_version']
        );

        $this->assertSame(
            1,
            $analysis->details['skills_detected']
        );

        $this->assertSame(
            0,
            $analysis->details['skills_configured']
        );

        $this->assertSame(
            1,
            $analysis->details['skills_total']
        );

        $missingAws =
            collect($analysis->missing_skills)
                ->firstWhere(
                    'name',
                    'AWS'
                );

        $this->assertNotNull($missingAws);

        $this->assertSame(
            'detected',
            $missingAws['job_source']
        );

        $this->assertSame(
            'preferred',
            $missingAws['importance']
        );

        $this->assertSame(
            50,
            $missingAws['weight']
        );

        $this->assertSame(
            25.0,
            (float) $missingAws['effective_weight']
        );
    }

    public function test_resume_skill_can_be_detected_from_alias_in_resume_text(): void
    {
        [$resumeVersion, $job] =
            $this->makeResumeAndJob(
                [],
                'Backend engineer experienced with Amazon Web Services.'
            );

        $aws = $this->makeSkill(
            'AWS',
            'Cloud'
        );

        $this->makeAlias(
            $aws,
            'Amazon Web Services'
        );

        $this->attachJobSkill(
            $job,
            $aws,
            'preferred',
            50,
            null
        );

        $analysis = $this->analyze(
            $resumeVersion,
            $job
        );

        $this->assertSame(
            1,
            $analysis->details['resume_skills_total']
        );

        $this->assertSame(
            0,
            $analysis->details['resume_skills_structured']
        );

        $this->assertSame(
            1,
            $analysis->details['resume_skills_detected']
        );

        $matchedAws =
            collect($analysis->matched_skills)
                ->firstWhere(
                    'name',
                    'AWS'
                );

        $this->assertNotNull($matchedAws);

        $this->assertSame(
            'detected',
            $matchedAws['resume_source']
        );

        $this->assertSame(
            'configured',
            $matchedAws['job_source']
        );

        $this->assertSame(
            100.0,
            (float) $analysis->skills_score
        );
    }

    public function test_overlapping_experience_is_not_counted_twice(): void
    {
        [$resumeVersion, $job] =
            $this->makeResumeAndJob([
                'minimum_experience_years' => 2,
            ]);

        ResumeExperience::create([
            'resume_version_id' =>
                $resumeVersion->id,
            'company_name' =>
                'Company A',
            'job_title' =>
                'Backend Developer',
            'location' =>
                'Remote',
            'start_date' =>
                '2024-01-01',
            'end_date' =>
                '2024-12-31',
            'is_current' =>
                false,
            'description' =>
                'Backend development.',
            'sort_order' =>
                0,
        ]);

        ResumeExperience::create([
            'resume_version_id' =>
                $resumeVersion->id,
            'company_name' =>
                'Company B',
            'job_title' =>
                'Software Engineer',
            'location' =>
                'Remote',
            'start_date' =>
                '2024-06-01',
            'end_date' =>
                '2025-06-30',
            'is_current' =>
                false,
            'description' =>
                'Software engineering.',
            'sort_order' =>
                1,
        ]);

        $analysis = $this->analyze(
            $resumeVersion,
            $job
        );

        $actualYears =
            (float) $analysis
                ->details['experience']['actual_years'];

        $this->assertGreaterThanOrEqual(
            1.49,
            $actualYears
        );

        $this->assertLessThanOrEqual(
            1.51,
            $actualYears
        );

        $this->assertFalse(
            $analysis
                ->details['experience']['meets_requirement']
        );

        $this->assertLessThan(
            100,
            (float) $analysis->experience_score
        );
    }

    public function test_education_requirement_is_matched_by_degree_rank(): void
    {
        [$resumeVersion, $job] =
            $this->makeResumeAndJob([
                'education_level' =>
                    'Bachelor',
            ]);

        ResumeEducation::create([
            'resume_version_id' =>
                $resumeVersion->id,
            'institution' =>
                'Test University',
            'degree' =>
                'Bachelor of Science',
            'field_of_study' =>
                'Computer Science',
            'location' =>
                'Test City',
            'start_date' =>
                '2020-01-01',
            'end_date' =>
                '2024-01-01',
            'description' =>
                'Computer Science degree.',
            'sort_order' =>
                0,
        ]);

        $analysis = $this->analyze(
            $resumeVersion,
            $job
        );

        $this->assertSame(
            100.0,
            (float) $analysis->education_score
        );

        $this->assertTrue(
            $analysis
                ->details['education']['meets_requirement']
        );

        $this->assertSame(
            'Bachelor of Science',
            $analysis
                ->details['education']['candidate_highest']
        );
    }

    public function test_keyword_engine_does_not_duplicate_alias_components(): void
    {
        [$resumeVersion, $job] =
            $this->makeResumeAndJob([
                'title' =>
                    'Cloud Engineer',
                'description' =>
                    'Experience with Amazon Web Services is preferred.',
                'requirements' =>
                    'Cloud infrastructure.',
            ]);

        $aws = $this->makeSkill(
            'AWS',
            'Cloud'
        );

        $this->makeAlias(
            $aws,
            'Amazon Web Services'
        );

        $analysis = $this->analyze(
            $resumeVersion,
            $job
        );

        $allKeywords =
            collect(
                array_merge(
                    $analysis->matched_keywords,
                    $analysis->missing_keywords
                )
            )
                ->map(
                    fn ($keyword) =>
                        mb_strtolower(
                            (string) $keyword
                        )
                )
                ->values();

        $this->assertTrue(
            $allKeywords->contains('aws')
        );

        $this->assertFalse(
            $allKeywords->contains('amazon')
        );

        $this->assertFalse(
            $allKeywords->contains('web')
        );

        $this->assertFalse(
            $allKeywords->contains('services')
        );
    }

    public function test_overall_score_equals_weighted_component_scores(): void
    {
        [$resumeVersion, $job] =
            $this->makeResumeAndJob([
                'minimum_experience_years' => 2,
                'education_level' => 'Bachelor',
            ]);

        $php = $this->makeSkill(
            'PHP',
            'Backend'
        );

        $this->attachJobSkill(
            $job,
            $php,
            'required',
            100,
            'intermediate'
        );

        $this->attachResumeSkill(
            $resumeVersion,
            $php,
            'intermediate'
        );

        $analysis = $this->analyze(
            $resumeVersion,
            $job
        );

        $expected = round(
            ((float) $analysis->skills_score * 0.35) +
            ((float) $analysis->experience_score * 0.25) +
            ((float) $analysis->keywords_score * 0.20) +
            ((float) $analysis->education_score * 0.10) +
            ((float) $analysis->structure_score * 0.10),
            2
        );

        $this->assertSame(
            $expected,
            (float) $analysis->overall_score
        );
    }

    private function analyze(
        ResumeVersion $resumeVersion,
        Job $job
    ): ATSAnalysis {
        $resumeVersion->unsetRelation('skills');
        $resumeVersion->unsetRelation('experiences');
        $resumeVersion->unsetRelation('educations');
        $resumeVersion->unsetRelation('languages');

        $job->unsetRelation('skills');

        return app(
            ATSAnalyzerService::class
        )->analyze(
            $resumeVersion,
            $job,
            null
        );
    }

    private function makeResumeAndJob(
        array $jobOverrides = [],
        string $summary = 'Backend engineer'
    ): array {
        $user =
            User::factory()->create();

        $resume =
            Resume::create([
                'user_id' =>
                    $user->id,
                'title' =>
                    'ATS Test Resume',
                'status' =>
                    'active',
            ]);

        $resumeVersion =
            ResumeVersion::create([
                'resume_id' =>
                    $resume->id,
                'version_number' =>
                    1,
                'status' =>
                    'active',
                'summary' =>
                    $summary,
                'template' =>
                    'default',
            ]);

        $resume->update([
            'current_version_id' =>
                $resumeVersion->id,
        ]);

        $defaults = [
            'created_by' =>
                $user->id,
            'title' =>
                'Backend Engineer',
            'company_name' =>
                'AST-CV',
            'location' =>
                'Remote',
            'employment_type' =>
                'full_time',
            'workplace_type' =>
                'remote',
            'description' =>
                'Backend engineering role.',
            'requirements' =>
                'Problem solving.',
            'minimum_experience_years' =>
                0,
            'education_level' =>
                null,
            'status' =>
                'active',
            'published_at' =>
                now(),
        ];

        $job =
            Job::create(
                array_merge(
                    $defaults,
                    $jobOverrides
                )
            );

        return [
            $resumeVersion,
            $job,
        ];
    }

    private function makeSkill(
        string $name,
        ?string $category = null
    ): Skill {
        return Skill::create([
            'name' => $name,
            'category' => $category,
        ]);
    }

    private function makeAlias(
        Skill $skill,
        string $alias
    ): SkillAlias {
        return SkillAlias::create([
            'skill_id' =>
                $skill->id,
            'alias' =>
                $alias,
            'normalized_alias' =>
                $this->normalizeAlias(
                    $alias
                ),
            'language' =>
                'en',
        ]);
    }

    private function attachJobSkill(
        Job $job,
        Skill $skill,
        string $importance,
        int $weight,
        ?string $minimumLevel
    ): void {
        $job->skills()->attach(
            $skill->id,
            [
                'importance' =>
                    $importance,
                'weight' =>
                    $weight,
                'minimum_level' =>
                    $minimumLevel,
            ]
        );
    }

    private function attachResumeSkill(
        ResumeVersion $resumeVersion,
        Skill $skill,
        ?string $level
    ): void {
        $resumeVersion
            ->skills()
            ->attach(
                $skill->id,
                [
                    'level' =>
                        $level,
                    'sort_order' =>
                        0,
                ]
            );
    }

    private function normalizeAlias(
        string $value
    ): string {
        $value =
            mb_strtolower(
                trim($value),
                'UTF-8'
            );

        $value = preg_replace(
            '/[^\p{L}\p{N}\+\#]+/u',
            ' ',
            $value
        );

        $value = preg_replace(
            '/\s+/u',
            ' ',
            $value
        );

        return trim($value);
    }
}
