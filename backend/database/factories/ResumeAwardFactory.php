<?php

namespace Database\Factories;

use App\Models\ResumeAward;
use App\Models\ResumeVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeAward>
 */
class ResumeAwardFactory extends Factory
{
    protected $model = ResumeAward::class;

    public function definition(): array
    {
        return [
            'resume_version_id' => ResumeVersion::factory(),
            'title' => fake()->sentence(3),
            'issuer' => fake()->company(),
            'description' => fake()->paragraph(),
            'awarded_at' => fake()->date(),
            'url' => fake()->url(),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}