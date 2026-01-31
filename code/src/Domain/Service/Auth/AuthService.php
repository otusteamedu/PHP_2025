<?php

declare(strict_types=1);

namespace App\Domain\Service\Auth;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\Session\SessionInterface;

class AuthService
{
    private const SESSION_KEY = 'user_id';
    private ?User $user = null;

    public function __construct(
        private SessionInterface $session,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function login(string $login, string $password): bool
    {
        //todo создать класс для сравнения паролей
        $user = $this->userRepository->findBy(['login' => $login, 'authkey' => $password]);

        if ($user) {
            $this->session->set(self::SESSION_KEY, $user->getId());
            $this->user = $user;
            return true;
        }

        return false;
    }

    public function logout(): void
    {
        $this->session->clear();
        $this->user = null;
    }

    public function isLoggedIn(): bool
    {
        return $this->getCurrentUser() !== null;
    }

    public function isAdmin(): bool
    {
        $user = $this->getCurrentUser();
        return $user && $user->isAdmin();
    }

    public function getCurrentUser(): ?User
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $userId = $this->session->get(self::SESSION_KEY);
        if ($userId) {
            $this->user = $this->userRepository->findById((int)$userId);
            return $this->user;
        }

        return null;
    }
}
