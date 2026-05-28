<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Application\DTO\PaginationDTO;
use App\Domain\Entity\User;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?object;

    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * @param int $page
     * @param int $limit
     * @return PaginationDTO
     */
    public function findAll(int $page = 1, int $limit = 10): PaginationDTO;

    /**
     * @param User $entity
     * @return User
     */
    public function save(object $entity): object;

    /**
     * @param User $entity
     */
    public function remove(object $entity): void;
}
