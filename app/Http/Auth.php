<?php
namespace App\Http;

use App\Models\UserModel;

class Auth
{
    public static function token(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';

        if ($header === '' && function_exists('getallheaders')) {
            $headers = array_change_key_case(getallheaders(), CASE_LOWER);
            $header = $headers['authorization'] ?? '';
        }

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

    public static function requireRole(string $role): array
    {
        $user = self::requireUser();

        if ($user['role'] !== $role) {
            $name = $role === 'student' ? 'een student' : 'een bedrijf';
            ApiResponse::error(ApiResponse::FORBIDDEN, "Alleen {$name} mag dit doen.");
        }

        return $user;
    }

    public static function requireOwner(int $ownerId): array
    {
        $user = self::requireRole('company');

        if ($user['id'] !== $ownerId) {
            ApiResponse::error(ApiResponse::FORBIDDEN, 'Dit is niet jouw vacature.');
        }

        return $user;
    }
}
