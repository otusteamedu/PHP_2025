<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\TrainingSchedule;

interface TrainingScheduleRepositoryInterface
{
    /**
     * @param int $trainingPlanId
     * @return TrainingSchedule[]
     */
    public function findByTrainingPlanId(int $trainingPlanId): array;

    public function save(TrainingSchedule $schedule): void;

    public function delete(TrainingSchedule $schedule): void;
}
