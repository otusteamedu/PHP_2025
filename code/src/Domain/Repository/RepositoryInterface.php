<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface RepositoryInterface
{
    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param int $id The identifier.
     * @return object|null The entity found, or null if not found.
     */
    public function findById(int $id): ?object;

    /**
     * Finds all entities in the repository.
     *
     * @param int $page
     * @param int $limit
     * @return array A list of entities.
     */
    public function findAll(int $page = 1, int $limit = 10): array;

    /**
     * Saves a given entity.
     *
     * @param object $entity The entity to save.
     * @return object The saved entity (possibly with updated ID/state).
     */
    public function save(object $entity): object;

    /**
     * Removes a given entity.
     *
     * @param object $entity The entity to remove.
     */
    public function remove(object $entity): void;
}
