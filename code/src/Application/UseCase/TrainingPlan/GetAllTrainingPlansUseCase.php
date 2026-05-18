<?php

declare(strict_types=1);

namespace App\Application\UseCase\TrainingPlan;

use App\Domain\Repository\TrainingPlanRepositoryInterface;


readonly class GetAllTrainingPlansUseCase
{
    public function __construct(
        private TrainingPlanRepositoryInterface $trainingPlanRepository
    ) {}

    /**
     * Retrieves all training plans.
     *
     * @return array An array of training plan data, each containing 'id', 'name', and 'description'.
     */

    public function execute(): array
    {
        $plans = $this->trainingPlanRepository->findAll();
        $result = [];
        foreach ($plans['plans'] as $plan) {
            $result[] = [
                'id' => $plan->getId(),
                'name' => $plan->getName(),
                'description' => $plan->getDescription(),
            ];
        }
        return $result;
    }
}