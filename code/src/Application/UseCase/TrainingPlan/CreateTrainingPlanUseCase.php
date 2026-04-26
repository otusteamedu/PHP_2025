<?php

declare(strict_types=1);

namespace App\Application\UseCase\TrainingPlan;

use App\Domain\Repository\TrainingPlanRepositoryInterface;
use App\Domain\Entity\TrainingPlan;

class CreateTrainingPlanUseCase
{
    public function __construct(private readonly TrainingPlanRepositoryInterface $trainingPlanRepository)
    {
    }

    public function execute(string $name, string $description): TrainingPlan
    {
        $plan = new TrainingPlan(null, $name, 'active', $description, new \DateTimeImmutable());
        return $this->trainingPlanRepository->save($plan);
    }
}
