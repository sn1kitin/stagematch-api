<?php
namespace App\Models;

class VacancyModel extends Model
{
    protected static string $table = 'vacancies';
    protected static array $fillable = ['company_id', 'title', 'description', 'location'];

    public static function withDetails(array $vacancy): array
    {
        $company = UserModel::find($vacancy['company_id']);
        $vacancy['company'] = $company === null ? null : ['id' => $company['id'], 'name' => $company['name']];
        $vacancy['skills'] = self::skills($vacancy['id']);

        return $vacancy;
    }

    public static function skills(int $vacancyId): array
    {
        self::db()->query(
            'SELECT s.`id`, s.`name` FROM `vacancy_skills` vs
             JOIN `skills` s ON s.`id` = vs.`skill_id`
             WHERE vs.`vacancy_id` = :id ORDER BY s.`name`',
            ['id' => $vacancyId]
        );

        return self::db()->fetchAll();
    }

    public static function setSkills(int $vacancyId, array $skillIds): void
    {
        self::db()->query('DELETE FROM `vacancy_skills` WHERE `vacancy_id` = :id', ['id' => $vacancyId]);

        foreach (array_unique($skillIds) as $skillId) {
            self::db()->query(
                'INSERT INTO `vacancy_skills` (`vacancy_id`, `skill_id`) VALUES (:vacancy, :skill)',
                ['vacancy' => $vacancyId, 'skill' => (int) $skillId]
            );
        }
    }
}
