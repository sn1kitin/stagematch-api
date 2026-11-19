<?php
namespace App\Models;

class UserModel extends Model
{
    protected static string $table = 'users';
    protected static array $fillable = ['name', 'email', 'password', 'role'];
    // wachtwoord, email en token mogen nooit in een response
    protected static string $columns = '`id`, `name`, `role`, `created_at`, `updated_at`';

    // met wachtwoord, alleen voor het inloggen
    public static function findByEmail(string $email): ?array
    {
        self::db()->query('SELECT * FROM `users` WHERE `email` = :email', ['email' => $email]);

        return self::db()->fetch();
    }

    // in de database staat alleen de hash van de token
    public static function findByToken(string $token): ?array
    {
        $columns = static::$columns;
        self::db()->query("SELECT {$columns} FROM `users` WHERE `api_token` = :token", ['token' => hash('sha256', $token)]);

        return self::db()->fetch();
    }

    public static function setToken(int $id, ?string $token): void
    {
        self::db()->query('UPDATE `users` SET `api_token` = :token WHERE `id` = :id', [
            'token' => $token === null ? null : hash('sha256', $token),
            'id' => $id,
        ]);
    }

    public static function emailTaken(string $email): bool
    {
        self::db()->query('SELECT `id` FROM `users` WHERE `email` = :email', ['email' => $email]);

        return self::db()->fetch() !== null;
    }
}
