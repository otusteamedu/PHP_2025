<?php
declare(strict_types=1);

namespace Infrastructure\Repository;

use Domain\Users\Entity\User;

class FileUserRepository
{
    public function findAll(): iterable
    {
        // TODO: Implement findAll() method.
        return [];
    }

    public function findById(int $id): ?User
    {
        // TODO: Implement findById() method.
        return null;
    }

    public function save(User $user): void
    {
        // Имитация сохранения в БД с присваиванием ID
        $reflectionProperty = new \ReflectionProperty(User::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 1);
    }

    public function delete(User $service): void
    {
        // TODO: Implement delete() method.
    }
}