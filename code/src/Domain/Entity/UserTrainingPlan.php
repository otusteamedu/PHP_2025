<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class UserTrainingPlan
{
    private int $userId;
    private int $trainingPlanId;

    public function __construct(int $userId, int $trainingPlanId)
    {
        $this->userId = $userId;
        $this->trainingPlanId = $trainingPlanId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTrainingPlanId(): int
    {
        return $this->trainingPlanId;
    }
}
