<?php

namespace Database\Factories;

use App\Models\Resume;
use App\Models\ResumeVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeVersion>
 */
class ResumeVersionFactory extends Factory
{
    protected $model = ResumeVersion::class;

    public function definition(): array
    {
        return [
            'resume_id' => Resume::factory(),
            'version_number' => 1,
            'status' => 'draft',
            'summary' => fake()->paragraph(),
            'template' => 'default',
        ];
    }
}