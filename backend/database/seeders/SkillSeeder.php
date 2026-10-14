<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['name' => 'PHP', 'category' => 'Backend'],
            ['name' => 'Laravel', 'category' => 'Backend'],
            ['name' => 'MySQL', 'category' => 'Database'],
            ['name' => 'PostgreSQL', 'category' => 'Database'],
            ['name' => 'SQL', 'category' => 'Database'],
            ['name' => 'REST API', 'category' => 'Backend'],
            ['name' => 'API Development', 'category' => 'Backend'],
            ['name' => 'Git', 'category' => 'Tools'],
            ['name' => 'GitHub', 'category' => 'Tools'],
            ['name' => 'Docker', 'category' => 'DevOps'],
            ['name' => 'Linux', 'category' => 'Operating Systems'],
            ['name' => 'Redis', 'category' => 'Backend'],
            ['name' => 'JavaScript', 'category' => 'Frontend'],
            ['name' => 'TypeScript', 'category' => 'Frontend'],
            ['name' => 'HTML', 'category' => 'Frontend'],
            ['name' => 'CSS', 'category' => 'Frontend'],
            ['name' => 'React', 'category' => 'Frontend'],
            ['name' => 'Vue.js', 'category' => 'Frontend'],
            ['name' => 'Node.js', 'category' => 'Backend'],
            ['name' => 'Python', 'category' => 'Programming'],
            ['name' => 'Java', 'category' => 'Programming'],
            ['name' => 'C#', 'category' => 'Programming'],
            ['name' => 'Flutter', 'category' => 'Mobile'],
            ['name' => 'Dart', 'category' => 'Mobile'],
            ['name' => 'Firebase', 'category' => 'Cloud'],
            ['name' => 'AWS', 'category' => 'Cloud'],
            ['name' => 'Azure', 'category' => 'Cloud'],
            ['name' => 'CI/CD', 'category' => 'DevOps'],
            ['name' => 'Unit Testing', 'category' => 'Testing'],
            ['name' => 'PHPUnit', 'category' => 'Testing'],
            ['name' => 'Cybersecurity', 'category' => 'Security'],
            ['name' => 'Network Security', 'category' => 'Security'],
            ['name' => 'Database Security', 'category' => 'Security'],
            ['name' => 'Machine Learning', 'category' => 'AI'],
            ['name' => 'Artificial Intelligence', 'category' => 'AI'],
            ['name' => 'Data Analysis', 'category' => 'Data'],
            ['name' => 'Problem Solving', 'category' => 'Soft Skills'],
            ['name' => 'Communication', 'category' => 'Soft Skills'],
            ['name' => 'Teamwork', 'category' => 'Soft Skills'],
            ['name' => 'Project Management', 'category' => 'Management'],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(
                ['name' => $skill['name']],
                ['category' => $skill['category']]
            );
        }
    }
}