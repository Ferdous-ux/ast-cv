<?php

namespace Database\Seeders;

use App\Models\ResumeTemplate;
use Illuminate\Database\Seeder;

class ResumeTemplateSeeder extends Seeder
{
    public function run(): void
    {
        ResumeTemplate::updateOrCreate(
            ['key' => 'default'],
            [
                'name' => 'Default',
                'description' => 'Default AST-CV resume template.',
                'thumbnail' => null,
                'is_active' => true,
                'sort_order' => 0,
            ]
        );
    }
}
