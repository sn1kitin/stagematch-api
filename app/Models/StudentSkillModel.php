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

    public static function has(int $studentId, int $skillId): bool
    {
        self::db()->query(
            'SELECT 1 FROM `student_skills` WHERE `user_id` = :user AND `skill_id` = :skill',
            ['user' => $studentId, 'skill' => $skillId]
        );

        return self::db()->fetch() !== null;
    }

    public static function add(int $studentId, int $skillId): void
    {
        self::db()->query(
            'INSERT INTO `student_skills` (`user_id`, `skill_id`) VALUES (:user, :skill)',
            ['user' => $studentId, 'skill' => $skillId]
        );
    }

    public static function remove(int $studentId, int $skillId): void
    {
        self::db()->query(
            'DELETE FROM `student_skills` WHERE `user_id` = :user AND `skill_id` = :skill',
            ['user' => $studentId, 'skill' => $skillId]
        );
    }
}
