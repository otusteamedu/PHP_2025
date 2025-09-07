<?php

namespace App\Application\Services;

use App\Infrastructure\Repositories\UserRepository;

class UserService
{
    public function __construct(
        private UserRepository $repository,
        private JwtService $jwtService
    ) {}

    public function registerUser(string $email, string $password): string
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $userId = $this->repository->save($email, $passwordHash);

        $jwt = $this->jwtService->getJwt($userId);
        return $jwt;
    }

    public function loginUser(string $email, string $password): string
    {
        $user = $this->repository->getByEmail($email);

        if (!$user) {
            return '';
        }

        if (!password_verify($password, $user['password'])) {
            return '';
        }

        $jwt = $this->jwtService->getJwt($user['id']);
        return $jwt;
    }
}