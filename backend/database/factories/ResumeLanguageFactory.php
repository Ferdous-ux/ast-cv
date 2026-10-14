<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\ResumeLanguage;
use App\Models\ResumeVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeLanguage>
 */
class ResumeLanguageFactory extends Factory
{
    protected $model = ResumeLanguage::class;

    public function definition(): array
    {
        return [
            'resume_version_id' => ResumeVersion::factory(),
            'language_id' => Language::factory(),
            'proficiency' => fake()->randomElement([
                'Basic',
                'Intermediate',
                'Advanced',
                'Native',
            ]),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}