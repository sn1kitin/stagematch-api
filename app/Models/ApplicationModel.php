<?php
namespace App\Models;

class ApplicationModel extends Model
{
    protected static string $table = 'applications';
    protected static array $fillable = ['vacancy_id', 'student_id', 'motivation', 'status'];

    public static function findFor(int $vacancyId, int $studentId): ?array
    {
        self::db()->query(
            'SELECT * FROM `applications` WHERE `vacancy_id` = :vacancy AND `student_id` = :student',
            ['vacancy' => $vacancyId, 'student' => $studentId]
        );

        return self::db()->fetch();
    }

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

    // alle sollicitaties op de vacatures van een bedrijf
    public static function forCompany(int $companyId): array
    {
        self::db()->query(
            'SELECT a.*, v.`title` AS vacancy_title, u.`name` AS student_name
             FROM `applications` a
             JOIN `vacancies` v ON v.`id` = a.`vacancy_id`
             JOIN `users` u ON u.`id` = a.`student_id`
             WHERE v.`company_id` = :id ORDER BY a.`created_at` DESC',
            ['id' => $companyId]
        );

        return self::db()->fetchAll();
    }

    public static function forVacancy(int $vacancyId): array
    {
        self::db()->query(
            'SELECT a.*, u.`name` AS student_name
             FROM `applications` a
             JOIN `users` u ON u.`id` = a.`student_id`
             WHERE a.`vacancy_id` = :id ORDER BY a.`created_at` DESC',
            ['id' => $vacancyId]
        );

        return self::db()->fetchAll();
    }
}
