<?php

namespace App\Modules\ATS\Services;

use App\Models\Skill;
use Illuminate\Support\Collection;

class SkillExtractionService
{
    public function extract(string $text): Collection
    {
        $normalizedText = $this->normalize($text);

        if ($normalizedText === '') {
            return collect();
        }

        $searchableText = ' ' . $normalizedText . ' ';

        $skills = Skill::with('aliases')
            ->orderBy('name')
            ->get();

        return $skills
            ->filter(function (Skill $skill) use ($searchableText) {
                $terms = collect([
                    $skill->name,
                    ...$skill->aliases->pluck('alias')->all(),
                ]);

                foreach ($terms as $term) {
                    $normalizedTerm = $this->normalize(
                        (string) $term
                    );

                    if ($normalizedTerm === '') {
                        continue;
                    }

                    if (
                        str_contains(
                            $searchableText,
                            ' ' . $normalizedTerm . ' '
                        )
                    ) {
                        return true;
                    }
                }

                return false;
            })
            ->values();
    }

    public function extractWithEvidence(
        string $text
    ): array {
        $normalizedText = $this->normalize($text);

        if ($normalizedText === '') {
            return [];
        }

        $searchableText = ' ' . $normalizedText . ' ';

        $skills = Skill::with('aliases')
            ->orderBy('name')
            ->get();

        $results = [];

        foreach ($skills as $skill) {
            $terms = collect([
                $skill->name,
                ...$skill->aliases->pluck('alias')->all(),
            ])
                ->map(fn ($term) => (string) $term)
                ->unique();

            foreach ($terms as $term) {
                $normalizedTerm = $this->normalize(
                    $term
                );

                if ($normalizedTerm === '') {
                    continue;
                }

                if (
                    str_contains(
                        $searchableText,
                        ' ' . $normalizedTerm . ' '
                    )
                ) {
                    $results[] = [
                        'skill_id' => $skill->id,
                        'skill_name' => $skill->name,
                        'matched_term' => $term,
                        'category' => $skill->category,
                    ];

                    break;
                }
            }
        }

        return $results;
    }

    private function normalize(?string $value): string
    {
        $value = mb_strtolower(
            trim((string) $value),
            'UTF-8'
        );

        $value = str_replace(
            ['أ', 'إ', 'آ'],
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