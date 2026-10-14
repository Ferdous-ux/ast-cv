<?php

namespace Database\Factories;

use App\Models\ResumePublication;
use App\Models\ResumeVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumePublication>
 */
class ResumePublicationFactory extends Factory
{
    protected $model = ResumePublication::class;

    public function definition(): array
    {
        return [
            'resume_version_id' => ResumeVersion::factory(),

            'title' => fake()->sentence(4),

            'publisher' => fake()->company(),

            'description' => fake()->paragraph(),

            'published_at' => fake()->date(),

            'url' => fake()->url(),

            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}