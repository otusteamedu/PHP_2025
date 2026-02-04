<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    /**
     * Поиск пользователя по заданным критериям.
     *
     * @param array $criteria
     * @return User|null
     */
    public function findBy(array $criteria): ?User;

    /**
     * Поиск пользователя по ID
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     *
     * @return User[]
     */
    public function findAll(int $limit, int $offset): array;
}
