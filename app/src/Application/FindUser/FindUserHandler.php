<?php

declare(strict_types=1);

namespace App\Application\FindUser;

use App\Domain\UserRepositoryInterface;

final readonly class FindUserHandler
{
    public function __construct(private UserRepositoryInterface $repository)
    {
    }

    /**
     * @throws \HttpResponseException
     */
    public function __invoke(FindUserQuery $query): FindUserOutput
    {
        $user = $this->repository->findById($query->id);

        if ($user === null) {
            throw new \HttpResponseException('User not found', 404);
        }

        return new FindUserOutput(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            password: $user->password,
            role: $user->role,
        );

    }
}
