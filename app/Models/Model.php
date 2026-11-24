<?php
namespace App\Models;

use App\Database\Database;

abstract class Model
{
    protected static string $table;
    protected static array $fillable = [];
    protected static string $columns = '*';

    private static ?Database $db = null;

    protected static function db(): Database
    {
        if (self::$db === null) {
            self::$db = new Database();
        }

        return self::$db;
    }

    protected static function onlyFillable(array $data): array
    {
        return array_intersect_key($data, array_flip(static::$fillable));
    }

    public static function all(): array
    {
        $table = static::$table;
        $columns = static::$columns;
        self::db()->query("SELECT {$columns} FROM `{$table}`");

        return self::db()->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $table = static::$table;
        $columns = static::$columns;
        self::db()->query("SELECT {$columns} FROM `{$table}` WHERE `id` = :id", ['id' => $id]);

        return self::db()->fetch();
    }

    public static function where(string $column, mixed $value): array
    {
        $table = static::$table;
        $columns = static::$columns;
        self::db()->query("SELECT {$columns} FROM `{$table}` WHERE `{$column}` = :value", ['value' => $value]);

        return self::db()->fetchAll();
    }

    public static function create(array $data): array
    {
        $table = static::$table;

        $data = static::onlyFillable($data);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $columns = array_keys($data);
        $columnList = '`' . implode('`, `', $columns) . '`';
        $placeholders = ':' . implode(', :', $columns);

        self::db()->query("INSERT INTO `{$table}` ({$columnList}) VALUES ({$placeholders})", $data);

        return static::find(self::db()->lastId());
    }

    public static function update(int $id, array $data): ?array
    {
        $table = static::$table;

        $data = static::onlyFillable($data);
        $data['updated_at'] = date('Y-m-d H:i:s');

        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "`{$column}` = :{$column}";
        }

        $data['id'] = $id;
        self::db()->query("UPDATE `{$table}` SET " . implode(', ', $sets) . " WHERE `id` = :id", $data);

        return static::find($id);
    }

    public static function delete(int $id): bool
    {
        $table = static::$table;
        self::db()->query("DELETE FROM `{$table}` WHERE `id` = :id", ['id' => $id]);

        return self::db()->rowCount() > 0;
    }
}
