<?php

namespace Database\Factories;

use App\Models\ResumeSkill;
use App\Models\ResumeVersion;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeSkill>
 */
class ResumeSkillFactory extends Factory
{
    protected $model = ResumeSkill::class;

    public function definition(): array
    {
        return [
            'resume_version_id' => ResumeVersion::factory(),
            'skill_id' => Skill::factory(),
            'level' => fake()->randomElement([
                'Beginner',
                'Intermediate',
                'Advanced',
                'Expert',
            ]),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}