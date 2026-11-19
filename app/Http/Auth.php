<?php
namespace App\Http;

use App\Models\UserModel;

class Auth
{
    // token uit de header: Authorization: Bearer <token>
    public static function token(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (preg_match('/^Bearer\s+(\S+)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function user(): ?array
    {
        $token = self::token();

        if ($token === null) {
            return null;
        }

        return UserModel::findByToken($token);
    }

    public static function requireUser(): array
    {
        $user = self::user();

        if ($user === null) {
            ApiResponse::error(ApiResponse::UNAUTHORIZED, 'Je moet ingelogd zijn. Stuur je token mee in de Authorization header.');
        }

        return $user;
    }
}
