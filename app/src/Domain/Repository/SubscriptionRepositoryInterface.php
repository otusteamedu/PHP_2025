<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Category;
use App\Domain\Entity\Subscription;
use App\Domain\ValueObject\Email;

interface SubscriptionRepositoryInterface
{
    /**
     * @return Subscription[]
     */
    public function findAll(): array;

    /**
     * @return Subscription[]
     */
    public function findAllByCategory(Category $category): array;

    public function findOneById(int $id): ?Subscription;

    public function findOneByCategoryAndEmail(Category $category, Email $email): ?Subscription;

    public function save(Subscription $subscription): void;
}
