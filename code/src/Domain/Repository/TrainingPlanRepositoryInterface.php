<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\TrainingPlan;

interface TrainingPlanRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int $id
     * @return TrainingPlan|null
     */
    public function findById(int $id): ?object;

    /**
     * @param int $userId
     * @return TrainingPlan[]
     */
    public function findByUserId(int $userId): array;

    /**
     * @param int $page
     * @param int $limit
     * @return TrainingPlan[]
     */
    public function findAll(int $page = 1, int $limit = 10): array;

    /**
     * @param TrainingPlan $entity
     * @return TrainingPlan
     */
    public function save(object $entity): object;

    /**
     * @param TrainingPlan $entity
     */
    public function remove(object $entity): void;
}
