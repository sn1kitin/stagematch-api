<?php
namespace App\Services;

class Matcher
{
    // $vacancySkills zoals uit VacancyModel::skills(), $studentSkillIds is een lijst met ids
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

        // geen gevraagde skills = iedereen past
        $percentage = count($vacancySkills) === 0 ? 100 : (int) round(count($matched) / count($vacancySkills) * 100);

        return [
            'match_percentage' => $percentage,
            'matched_skills' => $matched,
            'missing_skills' => $missing,
        ];
    }
}
