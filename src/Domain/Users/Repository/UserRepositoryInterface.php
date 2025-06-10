<?php
declare(strict_types=1);

namespace Domain\Users\Repository;

use Domain\Users\Entity\User;

interface UserRepositoryInterface
{
    /**
     * @return User[]
     */
    public function findAll(): iterable;

    public function findById(int $id): ?User;

    public function save(User $user): void;

    public function delete(User $user): void;
}