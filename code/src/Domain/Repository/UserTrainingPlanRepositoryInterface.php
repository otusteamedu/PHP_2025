<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface UserTrainingPlanRepositoryInterface
{
    public function assign(int $userId, int $trainingPlanId): void;
    public function unassign(int $userId, int $trainingPlanId): void;
    public function isAssigned(int $userId, int $trainingPlanId): bool;
}
