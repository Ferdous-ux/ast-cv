<?php

namespace Database\Seeders;

use App\Models\Resume;
use App\Models\ResumeExperience;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'test@astcv.local'],
            [
                'name' => 'AST Test User',
                'password' => 'password123',
            ]
        );

        $profile = $user->profile()->updateOrCreate(
            [],
            [
                'first_name' => 'AST',
                'last_name' => 'Test User',
                'headline' => 'Software Developer',
                'phone' => '+967700000000',
                'country' => 'Yemen',
                'city' => 'Sanaa',
            ]
        );

        $resume = $user->resumes()->updateOrCreate(
            ['title' => 'Software Developer Resume'],
            [
                'status' => 'draft',
            ]
        );

        $version = $resume->versions()->updateOrCreate(
            ['version_number' => 1],
            [
                'status' => 'draft',
                'summary' => 'Experienced software developer focused on building modern applications.',
                'template' => 'default',
            ]
        );

        $resume->update([
            'current_version_id' => $version->id,
        ]);

        $version->experiences()->updateOrCreate(
            [
                'company_name' => 'AST Company',
                'job_title' => 'Software Developer',
            ],
            [
                'start_date' => '2025-01-01',
                'is_current' => true,
                'description' => 'Developing and maintaining software applications.',
                'sort_order' => 1,
            ]
        );
    }
}
