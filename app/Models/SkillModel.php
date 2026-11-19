<?php
namespace App\Models;

class SkillModel extends Model
{
    protected static string $table = 'skills';

    public static function all(): array
    {
        self::db()->query('SELECT * FROM `skills` ORDER BY `name`');

        return self::db()->fetchAll();
    }
}
