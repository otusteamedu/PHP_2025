<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserService
{
    public function __construct(private UserRepository $repository) {}

    public function registerUser(string $email, string $password): string
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);;
        $userId = $this->repository->saveUser($email, $passwordHash);

        $jwt = $this->getJwt($userId);
        return $jwt;
    }

    private function getJwt(int $userId): string
    {
        $key = getenv('JWT_KEY');
        $url = getenv('APP_URL');

        $payload = [
            'iss' => $url,
            'userId' => $userId,
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }
}