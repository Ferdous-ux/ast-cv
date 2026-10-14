<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillAlias;
use Illuminate\Database\Seeder;

class SkillAliasSeeder extends Seeder
{
    public function run(): void
    {
        $aliases = [
            'PHP' => [
                'php',
                'php development',
            ],

            'Laravel' => [
                'laravel',
                'laravel framework',
                'laravel php',
            ],

            'MySQL' => [
                'mysql',
                'mysql database',
            ],

            'PostgreSQL' => [
                'postgresql',
                'postgres',
            ],

            'SQL' => [
                'sql',
                'structured query language',
            ],

            'REST API' => [
                'rest api',
                'restful api',
                'restful apis',
                'restful services',
                'web api',
                'web apis',
            ],

            'API Development' => [
                'api development',
                'api design',
                'api integration',
            ],

            'Git' => [
                'git',
                'git version control',
                'version control with git',
            ],

            'GitHub' => [
                'github',
                'github repository',
            ],

            'Docker' => [
                'docker',
                'docker containers',
                'docker containerization',
            ],

            'Linux' => [
                'linux',
                'linux administration',
            ],

            'Redis' => [
                'redis',
                'redis cache',
            ],

            'JavaScript' => [
                'javascript',
                'java script',
                'ecmascript',
            ],

            'TypeScript' => [
                'typescript',
                'type script',
            ],

            'HTML' => [
                'html',
                'html5',
            ],

            'CSS' => [
                'css',
                'css3',
            ],

            'React' => [
                'react',
                'react.js',
                'reactjs',
            ],

            'Vue.js' => [
                'vue',
                'vue.js',
                'vuejs',
            ],

            'Node.js' => [
                'node.js',
                'nodejs',
                'node js',
            ],

            'Python' => [
                'python',
                'python development',
            ],

            'Java' => [
                'java',
                'java development',
            ],

            'C#' => [
                'c#',
                'c sharp',
                'csharp',
            ],

            'Flutter' => [
                'flutter',
                'flutter framework',
            ],

            'Dart' => [
                'dart',
                'dart language',
            ],

            'Firebase' => [
                'firebase',
                'google firebase',
            ],

            'AWS' => [
                'aws',
                'amazon web services',
            ],

            'Azure' => [
                'azure',
                'microsoft azure',
            ],

            'CI/CD' => [
                'ci cd',
                'ci/cd',
                'continuous integration',
                'continuous delivery',
                'continuous deployment',
            ],

            'Unit Testing' => [
                'unit testing',
                'unit tests',
            ],

            'PHPUnit' => [
                'phpunit',
                'php unit',
            ],

            'Cybersecurity' => [
                'cybersecurity',
                'cyber security',
                'information security',
            ],

            'Network Security' => [
                'network security',
                'network cybersecurity',
            ],

            'Database Security' => [
                'database security',
                'db security',
            ],

            'Machine Learning' => [
                'machine learning',
                'ml',
            ],

            'Artificial Intelligence' => [
                'artificial intelligence',
                'ai',
            ],

            'Data Analysis' => [
                'data analysis',
                'data analytics',
            ],

            'Problem Solving' => [
                'problem solving',
                'problem-solving',
            ],

            'Communication' => [
                'communication',
                'communication skills',
            ],

            'Teamwork' => [
                'teamwork',
                'team work',
                'team collaboration',
            ],

            'Project Management' => [
                'project management',
                'project manager',
            ],
        ];

        foreach ($aliases as $skillName => $skillAliases) {
            $skill = Skill::where('name', $skillName)->first();

            if (!$skill) {
                continue;
            }

            foreach ($skillAliases as $alias) {
                SkillAlias::updateOrCreate(
                    [
                        'skill_id' => $skill->id,
                        'normalized_alias' => $this->normalize($alias),
                    ],
                    [
                        'alias' => $alias,
                        'language' => 'en',
                    ]
                );
            }
        }
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower(
            trim($value),
            'UTF-8'
        );

        $value = preg_replace(
            '/[^\p{L}\p{N}\+\#]+/u',
            ' ',
            $value
        );

        $value = preg_replace(
            '/\s+/u',
            ' ',
            $value
        );

        return trim($value);
    }
}