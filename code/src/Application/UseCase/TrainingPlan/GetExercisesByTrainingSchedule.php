<?php

declare(strict_types=1);

namespace App\Application\UseCase\TrainingPlan;

use App\Domain\Repository\TrainingPlanRepositoryInterface;

class GetExercisesByTrainingSchedule
{
    public function __construct(
        private readonly TrainingPlanRepositoryInterface $trainingPlanRepository
    ) {
    }

    public function execute(int $trainingScheduleId): array
    {
        return $this->trainingPlanRepository->findExercisesByTrainingScheduleId($trainingScheduleId);
    }
}
