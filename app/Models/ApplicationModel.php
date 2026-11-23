<?php
namespace App\Models;

class ApplicationModel extends Model
{
    protected static string $table = 'applications';
    protected static array $fillable = ['vacancy_id', 'student_id', 'motivation', 'status'];

    // sollicitaties van een student, met de vacature en het bedrijf erbij
    public static function forStudent(int $studentId): array
    {
        self::db()->query(
            'SELECT a.*, v.`title` AS vacancy_title, u.`name` AS company_name
             FROM `applications` a
             JOIN `vacancies` v ON v.`id` = a.`vacancy_id`
             JOIN `users` u ON u.`id` = v.`company_id`
             WHERE a.`student_id` = :id ORDER BY a.`created_at` DESC',
            ['id' => $studentId]
        );

        return self::db()->fetchAll();
    }
}
