<?php

namespace App\Modules\ATS\Services;

use App\Models\ResumeVersion;
use App\Models\SkillAlias;
use App\Models\User;
use App\Modules\ATS\Models\ATSAnalysis;
use App\Modules\Jobs\Models\Job;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ATSAnalyzerService
{
    private const WEIGHTS = [
        'skills' => 35,
        'experience' => 25,
        'keywords' => 20,
        'education' => 10,
        'structure' => 10,
    ];

    private SkillExtractionService $skillExtractor;

    public function __construct(
        SkillExtractionService $skillExtractor
    ) {
        $this->skillExtractor = $skillExtractor;
    }

    public function analyze(
        ResumeVersion $resumeVersion,
        Job $job,
        ?User $analyzedBy = null
    ): ATSAnalysis {
        $resumeVersion->loadMissing([
            'resume',
            'skills',
            'experiences',
            'educations',
            'languages',
        ]);

        $job->loadMissing('skills');

        $skills = $this->analyzeSkills($resumeVersion, $job);
        $experience = $this->analyzeExperience($resumeVersion, $job);
        $education = $this->analyzeEducation($resumeVersion, $job);
        $keywords = $this->analyzeKeywords($resumeVersion, $job);
        $structure = $this->analyzeStructure($resumeVersion);

        $overallScore = round(
            ($skills['score'] * self::WEIGHTS['skills'] / 100) +
            ($experience['score'] * self::WEIGHTS['experience'] / 100) +
            ($keywords['score'] * self::WEIGHTS['keywords'] / 100) +
            ($education['score'] * self::WEIGHTS['education'] / 100) +
            ($structure['score'] * self::WEIGHTS['structure'] / 100),
            2
        );

        $recommendations = $this->buildRecommendations(
            $skills,
            $experience,
            $education,
            $keywords,
            $structure
        );

        return DB::transaction(function () use (
            $resumeVersion,
            $job,
            $analyzedBy,
            $skills,
            $experience,
            $education,
            $keywords,
            $structure,
            $overallScore,
            $recommendations
        ) {
            return ATSAnalysis::create([
                'resume_version_id' => $resumeVersion->id,
                'job_posting_id' => $job->id,
                'analyzed_by' => $analyzedBy?->id,
                'overall_score' => $overallScore,
                'skills_score' => $skills['score'],
                'experience_score' => $experience['score'],
                'education_score' => $education['score'],
                'keywords_score' => $keywords['score'],
                'structure_score' => $structure['score'],
                'matched_skills' => $skills['matched'],
                'missing_skills' => $skills['missing'],
                'matched_keywords' => $keywords['matched'],
                'missing_keywords' => $keywords['missing'],
                'recommendations' => $recommendations,
                'details' => [
                    'engine_version' => 'ATS-3.1',
                    'weights' => self::WEIGHTS,
                    'experience' => $experience,
                    'education' => $education,
                    'structure' => $structure,
                    'skills_total' => $skills['total'],
                    'skills_matched' => count($skills['matched']),
                    'skills_required' => $skills['required_count'],
                    'skills_preferred' => $skills['preferred_count'],
                    'skills_configured' => $skills['configured_count'],
                    'skills_detected' => $skills['detected_count'],
                    'resume_skills_total' => $skills['resume_total_count'],
                    'resume_skills_structured' => $skills['resume_structured_count'],
                    'resume_skills_detected' => $skills['resume_detected_count'],
                    'skills_total_weight' => $skills['total_weight'],
                    'skills_earned_weight' => $skills['earned_weight'],
                    'keywords_total' => $keywords['total'],
                    'keywords_matched' => count($keywords['matched']),
                ],
                'status' => 'completed',
                'analyzed_at' => now(),
            ]);
        });
    }

    private function analyzeSkills(
        ResumeVersion $resumeVersion,
        Job $job
    ): array {
        $resumeText = $this->buildResumeText($resumeVersion);

        $jobText = implode(' ', [
            $job->title,
            $job->description,
            $job->requirements,
        ]);

        $resumeSkillMap = [];

        foreach ($resumeVersion->skills as $skill) {
            $resumeSkillMap[$skill->id] = [
                'skill' => $skill,
                'level' => $skill->pivot->level ?? null,
                'source' => 'structured',
            ];
        }

        $resumeStructuredCount = count($resumeSkillMap);
        $resumeDetectedCount = 0;

        $detectedResumeSkills =
            $this->skillExtractor->extract($resumeText);

        foreach ($detectedResumeSkills as $skill) {
            if (!isset($resumeSkillMap[$skill->id])) {
                $resumeSkillMap[$skill->id] = [
                    'skill' => $skill,
                    'level' => null,
                    'source' => 'detected',
                ];

                $resumeDetectedCount++;
            }
        }

        $jobSkillMap = [];

        foreach ($job->skills as $skill) {
            $importance =
                $skill->pivot->importance ?? 'required';

            $baseWeight = max(
                1,
                min(
                    100,
                    (int) ($skill->pivot->weight ?? 100)
                )
            );

            $jobSkillMap[$skill->id] = [
                'skill' => $skill,
                'importance' => $importance,
                'weight' => $baseWeight,
                'minimum_level' =>
                    $skill->pivot->minimum_level ?? null,
                'source' => 'configured',
            ];
        }

        $detectedJobSkills =
            $this->skillExtractor->extract($jobText);

        foreach ($detectedJobSkills as $skill) {
            if (!isset($jobSkillMap[$skill->id])) {
                $jobSkillMap[$skill->id] = [
                    'skill' => $skill,
                    'importance' => 'preferred',
                    'weight' => 50,
                    'minimum_level' => null,
                    'source' => 'detected',
                ];
            }
        }

        $matched = [];
        $missing = [];

        $totalWeight = 0.0;
        $earnedWeight = 0.0;

        $requiredCount = 0;
        $preferredCount = 0;
        $configuredCount = 0;
        $detectedCount = 0;

        foreach ($jobSkillMap as $jobSkillData) {
            $jobSkill = $jobSkillData['skill'];
            $importance = $jobSkillData['importance'];
            $baseWeight = $jobSkillData['weight'];
            $minimumLevel = $jobSkillData['minimum_level'];
            $source = $jobSkillData['source'];

            $importanceMultiplier =
                $importance === 'preferred'
                    ? 0.50
                    : 1.00;

            $effectiveWeight =
                $baseWeight * $importanceMultiplier;

            $totalWeight += $effectiveWeight;

            if ($importance === 'required') {
                $requiredCount++;
            } else {
                $preferredCount++;
            }

            if ($source === 'configured') {
                $configuredCount++;
            } else {
                $detectedCount++;
            }

            if (!isset($resumeSkillMap[$jobSkill->id])) {
                $missing[] = [
                    'id' => $jobSkill->id,
                    'name' => $jobSkill->name,
                    'importance' => $importance,
                    'weight' => $baseWeight,
                    'effective_weight' =>
                        round($effectiveWeight, 2),
                    'minimum_level' => $minimumLevel,
                    'job_source' => $source,
                ];

                continue;
            }

            $resumeSkillData =
                $resumeSkillMap[$jobSkill->id];

            $resumeLevel =
                $resumeSkillData['level'];

            $levelResult =
                $this->compareSkillLevels(
                    $resumeLevel,
                    $minimumLevel
                );

            $earnedForSkill =
                $effectiveWeight * $levelResult['factor'];

            $earnedWeight += $earnedForSkill;

            $matched[] = [
                'id' => $jobSkill->id,
                'name' => $jobSkill->name,
                'importance' => $importance,
                'weight' => $baseWeight,
                'effective_weight' =>
                    round($effectiveWeight, 2),
                'earned_weight' =>
                    round($earnedForSkill, 2),
                'resume_level' => $resumeLevel,
                'minimum_level' => $minimumLevel,
                'level_factor' =>
                    $levelResult['factor'],
                'meets_minimum_level' =>
                    $levelResult['meets_requirement'],
                'job_source' => $source,
                'resume_source' =>
                    $resumeSkillData['source'],
            ];
        }

        $score = $totalWeight > 0
            ? ($earnedWeight / $totalWeight) * 100
            : 0;

        return [
            'score' =>
                $this->limitScore($score),

            'matched' =>
                array_values($matched),

            'missing' =>
                array_values($missing),

            'total' =>
                count($jobSkillMap),

            'required_count' =>
                $requiredCount,

            'preferred_count' =>
                $preferredCount,

            'configured_count' =>
                $configuredCount,

            'detected_count' =>
                $detectedCount,

            'resume_total_count' =>
                count($resumeSkillMap),

            'resume_structured_count' =>
                $resumeStructuredCount,

            'resume_detected_count' =>
                $resumeDetectedCount,

            'total_weight' =>
                round($totalWeight, 2),

            'earned_weight' =>
                round($earnedWeight, 2),
        ];
    }

    private function analyzeExperience(
        ResumeVersion $resumeVersion,
        Job $job
    ): array {
        $actualYears =
            $this->calculateExperienceYears(
                $resumeVersion
            );

        $requiredYears =
            (float) (
                $job->minimum_experience_years ?? 0
            );

        if ($requiredYears <= 0) {
            $score = 100;
        } else {
            $score = min(
                100,
                ($actualYears / $requiredYears) * 100
            );
        }

        return [
            'score' =>
                $this->limitScore($score),

            'actual_years' =>
                $actualYears,

            'required_years' =>
                $requiredYears,

            'meets_requirement' =>
                $requiredYears <= 0 ||
                $actualYears >= $requiredYears,
        ];
    }

    private function calculateExperienceYears(
        ResumeVersion $resumeVersion
    ): float {
        $intervals = [];

        foreach (
            $resumeVersion->experiences
            as $experience
        ) {
            if (!$experience->start_date) {
                continue;
            }

            $start =
                Carbon::parse(
                    $experience->start_date
                )->startOfDay();

            $end =
                $experience->is_current ||
                !$experience->end_date
                    ? now()->endOfDay()
                    : Carbon::parse(
                        $experience->end_date
                    )->endOfDay();

            if ($end->lt($start)) {
                continue;
            }

            $intervals[] = [
                'start' => $start,
                'end' => $end,
            ];
        }

        if (empty($intervals)) {
            return 0;
        }

        usort(
            $intervals,
            fn ($a, $b) =>
                $a['start']->timestamp <=>
                $b['start']->timestamp
        );

        $merged = [];

        foreach ($intervals as $interval) {
            if (empty($merged)) {
                $merged[] = $interval;
                continue;
            }

            $lastIndex = count($merged) - 1;
            $last = $merged[$lastIndex];

            if (
                $interval['start']->lte(
                    $last['end']->copy()->addDay()
                )
            ) {
                if ($interval['end']->gt($last['end'])) {
                    $merged[$lastIndex]['end'] =
                        $interval['end'];
                }
            } else {
                $merged[] = $interval;
            }
        }

        $totalDays = 0;

        foreach ($merged as $interval) {
            $totalDays +=
                $interval['start']->diffInDays(
                    $interval['end']
                ) + 1;
        }

        return round(
            $totalDays / 365.25,
            2
        );
    }

    private function analyzeEducation(
        ResumeVersion $resumeVersion,
        Job $job
    ): array {
        $required = trim(
            (string) $job->education_level
        );

        if ($required === '') {
            return [
                'score' => 100,
                'required' => null,
                'candidate_highest' => null,
                'meets_requirement' => true,
            ];
        }

        if (
            $resumeVersion
                ->educations
                ->isEmpty()
        ) {
            return [
                'score' => 0,
                'required' => $required,
                'candidate_highest' => null,
                'meets_requirement' => false,
            ];
        }

        $requiredRank =
            $this->educationRank($required);

        $candidateRank = 0;
        $candidateHighest = null;

        foreach (
            $resumeVersion->educations
            as $education
        ) {
            $degree = trim(
                (string) $education->degree
            );

            $rank =
                $this->educationRank($degree);

            if ($rank > $candidateRank) {
                $candidateRank = $rank;
                $candidateHighest = $degree;
            }
        }

        if ($requiredRank > 0) {
            if ($candidateRank >= $requiredRank) {
                $score = 100;
            } elseif (
                $candidateRank > 0 &&
                $candidateRank === $requiredRank - 1
            ) {
                $score = 60;
            } else {
                $score = 20;
            }

            return [
                'score' => $score,
                'required' => $required,
                'candidate_highest' =>
                    $candidateHighest,
                'meets_requirement' =>
                    $candidateRank >= $requiredRank,
            ];
        }

        $educationText =
            $this->normalize(
                $resumeVersion
                    ->educations
                    ->map(function ($education) {
                        return implode(' ', [
                            $education->institution,
                            $education->degree,
                            $education->field_of_study,
                            $education->description,
                        ]);
                    })
                    ->implode(' ')
            );

        $meets = str_contains(
            $educationText,
            $this->normalize($required)
        );

        return [
            'score' => $meets ? 100 : 50,
            'required' => $required,
            'candidate_highest' => $candidateHighest,
            'meets_requirement' => $meets,
        ];
    }

    private function educationRank(
        string $degree
    ): int {
        $degree = $this->normalize($degree);

        $levels = [
            5 => [
                'phd',
                'doctorate',
                'doctoral',
                'دكتوراه',
            ],

            4 => [
                'master',
                'masters',
                'msc',
                'ma',
                'ماجستير',
            ],

            3 => [
                'bachelor',
                'bachelors',
                'bsc',
                'ba',
                'بكالوريوس',
                'ليسانس',
            ],

            2 => [
                'diploma',
                'associate',
                'دبلوم',
            ],

            1 => [
                'high school',
                'secondary',
                'ثانويه',
                'ثانوي',
            ],
        ];

        foreach ($levels as $rank => $names) {
            foreach ($names as $name) {
                if (
                    str_contains(
                        $degree,
                        $this->normalize($name)
                    )
                ) {
                    return $rank;
                }
            }
        }

        return 0;
    }

    private function compareSkillLevels(
        ?string $resumeLevel,
        ?string $minimumLevel
    ): array {
        if (blank($minimumLevel)) {
            return [
                'factor' => 1.0,
                'meets_requirement' => true,
            ];
        }

        $requiredRank =
            $this->skillLevelRank($minimumLevel);

        if ($requiredRank === 0) {
            return [
                'factor' => 1.0,
                'meets_requirement' => true,
            ];
        }

        $resumeRank =
            $this->skillLevelRank($resumeLevel);

        if ($resumeRank === 0) {
            return [
                'factor' => 0.60,
                'meets_requirement' => false,
            ];
        }

        if ($resumeRank >= $requiredRank) {
            return [
                'factor' => 1.0,
                'meets_requirement' => true,
            ];
        }

        $difference =
            $requiredRank - $resumeRank;

        $factor = match ($difference) {
            1 => 0.65,
            2 => 0.35,
            default => 0.15,
        };

        return [
            'factor' => $factor,
            'meets_requirement' => false,
        ];
    }

    private function skillLevelRank(
        ?string $level
    ): int {
        $level = $this->normalize($level);

        if ($level === '') {
            return 0;
        }

        $levels = [
            1 => [
                'beginner',
                'basic',
                'entry',
                'junior',
                'مبتدئ',
            ],

            2 => [
                'intermediate',
                'mid',
                'medium',
                'متوسط',
            ],

            3 => [
                'advanced',
                'senior',
                'متقدم',
            ],

            4 => [
                'expert',
                'professional',
                'خبير',
                'محترف',
            ],
        ];

        foreach ($levels as $rank => $names) {
            foreach ($names as $name) {
                if (
                    $level === $this->normalize($name)
                ) {
                    return $rank;
                }
            }
        }

        return 0;
    }

    private function analyzeKeywords(
        ResumeVersion $resumeVersion,
        Job $job
    ): array {
        $jobText = implode(' ', [
            $job->title,
            $job->description,
            $job->requirements,
        ]);

        $resumeText =
            $this->buildResumeText($resumeVersion);

        $detectedJobSkills =
            $this->skillExtractor->extract($jobText);

        $allJobSkills =
            $job->skills
                ->concat($detectedJobSkills)
                ->unique('id')
                ->values();

        $skillKeywords =
            $allJobSkills
                ->pluck('name')
                ->filter()
                ->map(fn ($name) => trim((string) $name))
                ->unique(
                    fn ($name) =>
                        $this->normalizeKeywordText($name)
                )
                ->values()
                ->all();

        $skillKeywordIdMap = [];

        foreach ($allJobSkills as $skill) {
            $skillKeywordIdMap[
                $this->normalizeKeywordText($skill->name)
            ] = $skill->id;
        }

        $textKeywords =
            $this->extractKeywords($jobText, 30);

        /*
         * الكلمات التي تمثل أسماء المهارات أو aliases لها
         * لا يجب أن تتكرر كـ Keywords مستقلة.
         *
         * Amazon Web Services => AWS فقط
         * Laravel Framework   => Laravel فقط
         * RESTful APIs        => REST API فقط
         */
        $skillComponents = [];

        foreach ($skillKeywords as $skillKeyword) {
            $this->addKeywordComponents(
                $skillComponents,
                $skillKeyword
            );
        }

        $normalizedJobText =
            $this->normalizeKeywordText($jobText);

        $jobSkillIds =
            $allJobSkills
                ->pluck('id')
                ->filter()
                ->values()
                ->all();

        if (!empty($jobSkillIds)) {
            $aliases =
                SkillAlias::query()
                    ->whereIn('skill_id', $jobSkillIds)
                    ->pluck('alias');

            foreach ($aliases as $alias) {
                if (
                    $this->containsKeyword(
                        $normalizedJobText,
                        (string) $alias
                    )
                ) {
                    $this->addKeywordComponents(
                        $skillComponents,
                        (string) $alias
                    );
                }
            }
        }

        $textKeywords = array_values(
            array_filter(
                $textKeywords,
                fn ($keyword) =>
                    !isset(
                        $skillComponents[
                            $this->normalizeKeywordText($keyword)
                        ]
                    )
            )
        );

        $keywords = [];

        foreach (
            array_merge(
                $skillKeywords,
                $textKeywords
            ) as $keyword
        ) {
            $normalized =
                $this->normalizeKeywordText($keyword);

            if ($normalized === '') {
                continue;
            }

            if (!isset($keywords[$normalized])) {
                $keywords[$normalized] = $keyword;
            }
        }

        $keywords = array_slice(
            array_values($keywords),
            0,
            50
        );

        if (empty($keywords)) {
            return [
                'score' => 0,
                'matched' => [],
                'missing' => [],
                'total' => 0,
            ];
        }

        $normalizedResume =
            $this->normalizeKeywordText($resumeText);

        $resumeSkillIds =
            $resumeVersion
                ->skills
                ->pluck('id')
                ->merge(
                    $this->skillExtractor
                        ->extract($resumeText)
                        ->pluck('id')
                )
                ->unique()
                ->values()
                ->all();

        $resumeSkillIds = array_flip($resumeSkillIds);

        $matched = [];
        $missing = [];

        foreach ($keywords as $keyword) {
            $normalizedKeyword =
                $this->normalizeKeywordText($keyword);

            if (
                isset(
                    $skillKeywordIdMap[
                        $normalizedKeyword
                    ]
                )
            ) {
                $skillId =
                    $skillKeywordIdMap[
                        $normalizedKeyword
                    ];

                $isMatched =
                    isset($resumeSkillIds[$skillId]);
            } else {
                $isMatched =
                    $this->containsKeyword(
                        $normalizedResume,
                        $keyword
                    );
            }

            if ($isMatched) {
                $matched[] = $keyword;
            } else {
                $missing[] = $keyword;
            }
        }

        $score =
            (count($matched) / count($keywords)) * 100;

        return [
            'score' =>
                $this->limitScore($score),

            'matched' =>
                array_values($matched),

            'missing' =>
                array_values($missing),

            'total' =>
                count($keywords),
        ];
    }

    private function addKeywordComponents(
        array &$components,
        string $value
    ): void {
        $normalized =
            $this->normalizeKeywordText($value);

        if ($normalized === '') {
            return;
        }

        foreach (
            preg_split(
                '/\s+/u',
                $normalized,
                -1,
                PREG_SPLIT_NO_EMPTY
            ) as $component
        ) {
            $components[$component] = true;
        }
    }

    private function containsKeyword(
        string $normalizedResumeText,
        string $keyword
    ): bool {
        $keyword =
            $this->normalizeKeywordText($keyword);

        if ($keyword === '') {
            return false;
        }

        return str_contains(
            ' ' . $normalizedResumeText . ' ',
            ' ' . $keyword . ' '
        );
    }

    private function buildResumeText(
        ResumeVersion $resumeVersion
    ): string {
        $parts = [
            $resumeVersion->summary,

            $resumeVersion->skills
                ->pluck('name')
                ->implode(' '),
        ];

        foreach (
            $resumeVersion->experiences
            as $experience
        ) {
            $parts[] = implode(' ', [
                $experience->job_title,
                $experience->company_name,
                $experience->description,
                $experience->location,
            ]);
        }

        foreach (
            $resumeVersion->educations
            as $education
        ) {
            $parts[] = implode(' ', [
                $education->institution,
                $education->degree,
                $education->field_of_study,
                $education->description,
            ]);
        }

        foreach (
            $resumeVersion->languages
            as $language
        ) {
            $parts[] = $language->name ?? '';
        }

        return implode(
            ' ',
            array_filter($parts)
        );
    }

    private function extractKeywords(
        string $text,
        int $limit = 30
    ): array {
        $text =
            $this->normalizeKeywordText($text);

        $tokens = preg_split(
            '/\s+/u',
            $text,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $stopWords = array_flip([
            'a',
            'an',
            'the',
            'and',
            'or',
            'but',
            'if',
            'of',
            'to',
            'for',
            'with',
            'without',
            'from',
            'into',
            'on',
            'at',
            'by',
            'in',
            'as',
            'is',
            'are',
            'was',
            'were',
            'be',
            'been',
            'being',
            'this',
            'that',
            'these',
            'those',
            'it',
            'its',
            'we',
            'our',
            'us',
            'you',
            'your',
            'they',
            'their',
            'them',
            'who',
            'which',
            'what',
            'where',
            'when',
            'how',
            'have',
            'has',
            'had',
            'having',
            'will',
            'would',
            'should',
            'could',
            'can',
            'may',
            'must',
            'need',
            'needs',
            'needed',
            'looking',
            'seeking',
            'join',
            'joining',
            'candidate',
            'candidates',
            'company',
            'companies',
            'role',
            'position',
            'job',
            'jobs',
            'work',
            'working',
            'team',
            'teams',
            'required',
            'requirement',
            'requirements',
            'preferred',
            'experience',
            'experiences',
            'experienced',
            'years',
            'year',
            'skills',
            'skill',
            'knowledge',
            'ability',
            'abilities',
            'responsible',
            'responsibilities',
            'strong',
            'good',
            'excellent',
            'using',
            'use',
            'used',
            'plus',
            'including',
            'include',
            'includes',
            'developer',
            'developers',
            'development',
            'software',
            'degree',
            'bachelor',
            'bachelors',
            'master',
            'masters',
            'education',
            'qualification',
            'qualifications',

            'في',
            'من',
            'الى',
            'إلى',
            'على',
            'عن',
            'مع',
            'او',
            'أو',
            'و',
            'هذا',
            'هذه',
            'ذلك',
            'تلك',
            'الذي',
            'التي',
            'الذين',
            'يجب',
            'يمكن',
            'يكون',
            'تكون',
            'نبحث',
            'مطلوب',
            'المطلوب',
            'المطلوبه',
            'متطلبات',
            'متطلب',
            'وظيفه',
            'وظائف',
            'العمل',
            'خبره',
            'سنوات',
            'سنه',
            'مهاره',
            'مهارات',
            'معرفه',
            'قدره',
            'المرشح',
            'المرشحين',
            'الشركه',
            'فريق',
            'فريقنا',
            'بكالوريوس',
            'ماجستير',
            'مؤهل',
            'المؤهل',
            'تعليم',
        ]);

        $allowedShortKeywords = [
            'ai',
            'ui',
            'ux',
            'qa',
            'go',
            'c#',
            'c++',
            'php',
            'sql',
            'api',
        ];

        $keywords = [];

        foreach ($tokens as $token) {
            $token = trim($token);

            if ($token === '') {
                continue;
            }

            if (is_numeric($token)) {
                continue;
            }

            if (isset($stopWords[$token])) {
                continue;
            }

            if (
                mb_strlen($token) < 3 &&
                !in_array(
                    $token,
                    $allowedShortKeywords,
                    true
                )
            ) {
                continue;
            }

            if (!isset($keywords[$token])) {
                $keywords[$token] = true;
            }

            if (count($keywords) >= $limit) {
                break;
            }
        }

        return array_keys($keywords);
    }

    private function analyzeStructure(
        ResumeVersion $resumeVersion
    ): array {
        $sections = [
            'summary' =>
                filled($resumeVersion->summary),

            'skills' =>
                $resumeVersion->skills->isNotEmpty(),

            'experience' =>
                $resumeVersion->experiences->isNotEmpty(),

            'education' =>
                $resumeVersion->educations->isNotEmpty(),

            'languages' =>
                $resumeVersion->languages->isNotEmpty(),
        ];

        $completed = count(
            array_filter($sections)
        );

        return [
            'score' => round(
                (
                    $completed /
                    count($sections)
                ) * 100,
                2
            ),

            'sections' => $sections,

            'completed_sections' =>
                $completed,

            'total_sections' =>
                count($sections),
        ];
    }

    private function buildRecommendations(
        array $skills,
        array $experience,
        array $education,
        array $keywords,
        array $structure
    ): array {
        $recommendations = [];

        if (!empty($skills['missing'])) {
            $recommendations[] = [
                'code' => 'add_missing_skills',

                'skills' => array_values(
                    array_map(
                        fn ($skill) =>
                            $skill['name'],
                        $skills['missing']
                    )
                ),
            ];
        }

        if (!$experience['meets_requirement']) {
            $recommendations[] = [
                'code' =>
                    'increase_experience_match',

                'actual_years' =>
                    $experience['actual_years'],

                'required_years' =>
                    $experience['required_years'],
            ];
        }

        if (!$education['meets_requirement']) {
            $recommendations[] = [
                'code' =>
                    'education_requirement_not_met',

                'required' =>
                    $education['required'],
            ];
        }

        if (!empty($keywords['missing'])) {
            $recommendations[] = [
                'code' =>
                    'add_missing_keywords',

                'keywords' => array_slice(
                    $keywords['missing'],
                    0,
                    15
                ),
            ];
        }

        foreach (
            $structure['sections']
            as $section => $exists
        ) {
            if (!$exists) {
                $recommendations[] = [
                    'code' =>
                        'complete_section',

                    'section' =>
                        $section,
                ];
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = [
                'code' => 'strong_match',
            ];
        }

        return $recommendations;
    }

    private function normalizeKeywordText(
        ?string $value
    ): string {
        $value = $this->normalize($value);

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

    private function normalize(
        ?string $value
    ): string {
        $value = mb_strtolower(
            trim((string) $value),
            'UTF-8'
        );

        $value = str_replace(
            [
                'أ',
                'إ',
                'آ',
            ],
            'ا',
            $value
        );

        $value = str_replace(
            'ى',
            'ي',
            $value
        );

        $value = str_replace(
            'ة',
            'ه',
            $value
        );

        $value = preg_replace(
            '/[\x{064B}-\x{065F}\x{0670}]/u',
            '',
            $value
        );

        $value = preg_replace(
            '/\s+/u',
            ' ',
            $value
        );

        return trim($value);
    }

    private function limitScore(
        float|int $score
    ): float {
        return round(
            max(
                0,
                min(
                    100,
                    (float) $score
                )
            ),
            2
        );
    }
}
