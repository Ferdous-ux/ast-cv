<?php

namespace Database\Factories;

use App\Models\ResumeExperience;
use App\Models\ResumeVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeExperience>
 */
class ResumeExperienceFactory extends Factory
{
    protected $model = ResumeExperience::class;

    public function definition(): array
    {
        return [
            'resume_version_id' => ResumeVersion::factory(),
            'company_name' => fake()->company(),
            'job_title' => fake()->jobTitle(),
            'location' => fake()->city(),
            'start_date' => fake()->dateTimeBetween('-5 years', '-2 years')->format('Y-m-d'),
            'end_date' => null,
            'is_current' => true,
            'description' => fake()->paragraph(),
            'sort_order' => 0,
        ];
    }
}