<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\User;

interface UserTrainingPlanRepositoryInterface
{
    /**
     * @param int $trainingPlanId
     * @return User[]
     */
    public function findByTrainingPlanId(int $trainingPlanId): array;

    public function attachUserToTrainingPlan(int $userId, int $trainingPlanId);
}
