<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistense\Repository;

use App\Domain\User;
use App\Domain\UserRepositoryInterface;
use App\Infrastructure\Persistense\Mapping\UserMapper;

readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(private UserMapper $userMapper)
    {

    }

    public function save(User $user): void
    {
        $this->userMapper->save($user);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userMapper->findByEmail($email);
    }

    public function deleteById(int $id): void
    {
        $this->userMapper->deleteById($id);
    }

    public function find(int $id): ?User
    {
        return $this->userMapper->find($id);
    }

    public function findById($id): void
    {
        $this->userMapper->findById($id);
    }
}
