<?php

declare(strict_types=1);

namespace App\Application\DeleteUser;

use App\Domain\UserRepositoryInterface;

final readonly class DeleteUserHandler
{
    public function __construct(private UserRepositoryInterface $repository)
    {

    }

    public function __invoke(DeleteUserCommand $command): void
    {
        $this->repository->deleteById($command->id);
    }
}