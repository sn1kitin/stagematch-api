<?php
namespace App\Models;

class StudentSkillModel extends Model
{
    protected static string $table = 'student_skills';

    public static function forStudent(int $studentId): array
    {
        self::db()->query(
            'SELECT s.`id`, s.`name` FROM `student_skills` ss
             JOIN `skills` s ON s.`id` = ss.`skill_id`
             WHERE ss.`user_id` = :id ORDER BY s.`name`',
            ['id' => $studentId]
        );

        return self::db()->fetchAll();
    }

    public static function add(int $studentId, int $skillId): void
    {
        self::db()->query(
            'INSERT INTO `student_skills` (`user_id`, `skill_id`) VALUES (:user, :skill)',
            ['user' => $studentId, 'skill' => $skillId]
        );
    }
}
