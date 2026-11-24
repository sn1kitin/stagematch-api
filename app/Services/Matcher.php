<?php
namespace App\Services;

class Matcher
{
    public static function match(array $vacancySkills, array $studentSkillIds): array
    {
        $matched = [];
        $missing = [];

        foreach ($vacancySkills as $skill) {
            if (in_array($skill['id'], $studentSkillIds)) {
                $matched[] = $skill;
            } else {
                $missing[] = $skill;
            }
        }

        $percentage = count($vacancySkills) === 0 ? 100 : (int) round(count($matched) / count($vacancySkills) * 100);

        return [
            'match_percentage' => $percentage,
            'matched_skills' => $matched,
            'missing_skills' => $missing,
        ];
    }
}
