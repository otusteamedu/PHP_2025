<?php

declare(strict_types=1);

namespace App\Application\ActualizeUser;

use App\Domain\User;
use App\Domain\UserRepositoryInterface;

final readonly class ActualizeUserHandler
{
    public function __construct(private UserRepositoryInterface $repository)
    {
    }

    public function __invoke(ActualizeUserQuery $command): ActualizeUserOutput
    {
        $user = $this->repository->findByEmail($command->email);
        if ($user !== null) {
            $user->update($command->name, $command->email, $command->password, $command->role);
        } else {
            $user = User::create($command->name, $command->email, $command->password, $command->role);
            $this->repository->save($user);
        }

        return new ActualizeUserOutput($user->id);
    }
}
