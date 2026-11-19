<?php
namespace App\Models;

class VacancyModel extends Model
{
    protected static string $table = 'vacancies';
    protected static array $fillable = ['company_id', 'title', 'description', 'location'];

    // vacature met de naam van het bedrijf erbij
    public static function withDetails(array $vacancy): array
    {
        self::db()->query('SELECT `id`, `name` FROM `users` WHERE `id` = :id', ['id' => $vacancy['company_id']]);
        $vacancy['company'] = self::db()->fetch();

        return $vacancy;
    }
}
