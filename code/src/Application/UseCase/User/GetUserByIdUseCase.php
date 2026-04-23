<?php

declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Entity\User;

readonly class GetUserByIdUseCase
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function execute(int $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }
}
