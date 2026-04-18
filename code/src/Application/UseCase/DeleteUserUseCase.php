<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Exception\UserNotFoundException;
use App\Repository\UserRepositoryInterface;

final class DeleteUserUseCase
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function execute(int $id): void
    {
        $user = $this->userRepository->findById($id);

        if ($user === null) {
            throw new UserNotFoundException($id);
        }

        $this->userRepository->remove($user);
    }
}
