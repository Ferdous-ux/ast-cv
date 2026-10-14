<?php

namespace Database\Factories;

use App\Models\ResumeLink;
use App\Models\ResumeVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeLink>
 */
class ResumeLinkFactory extends Factory
{
    protected $model = ResumeLink::class;

    public function definition(): array
    {
        return [
            'resume_version_id' => ResumeVersion::factory(),
            'label' => fake()->randomElement([
                'LinkedIn',
                'GitHub',
                'Portfolio',
                'Website',
                'Behance',
            ]),
            'url' => fake()->url(),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}