<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Category;

interface CategoryRepositoryInterface
{
    /**
     * @return Category[]
     */
    public function findAll(): array;

    public function findOneById(int $id): ?Category;

    public function findOneByName(string $name): ?Category;

    public function save(Category $category): void;
}
