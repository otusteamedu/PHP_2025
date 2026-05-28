<?php

declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Entity\User;

readonly class DeleteUserUseCase
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function execute(int $userId): void
    {
        $user = $this->userRepository->findById($userId);
        if ($user) {
            $this->userRepository->remove($user);
        }
    }
}
