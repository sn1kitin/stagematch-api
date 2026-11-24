<?php
namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Auth;
use App\Models\UserModel;

class AuthController extends Controller
{
    public function register(array $data): void
    {
        $this->validate($data, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required|in:student,company',
        ]);

        if (UserModel::emailTaken($data['email'])) {
            ApiResponse::error(ApiResponse::UNPROCESSABLE, 'De invoer is niet geldig.', ['email' => 'Dit emailadres is al in gebruik.']);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        ApiResponse::send(UserModel::create($data), ApiResponse::CREATED);
    }

    public function login(array $data): void
    {
        $this->validate($data, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = UserModel::findByEmail($data['email']);

        if ($user === null || !password_verify($data['password'], $user['password'])) {
            ApiResponse::error(ApiResponse::UNAUTHORIZED, 'Email of wachtwoord klopt niet.');
        }

        $token = bin2hex(random_bytes(32));
        UserModel::setToken($user['id'], $token);

        ApiResponse::send([
            'token' => $token,
            'user' => UserModel::find($user['id']),
        ]);
    }

    public function logout(): void
    {
        $user = Auth::requireUser();
        UserModel::setToken($user['id'], null);

        ApiResponse::send(null);
    }

    public function me(): void
    {
        ApiResponse::send(Auth::requireUser());
    }
}
