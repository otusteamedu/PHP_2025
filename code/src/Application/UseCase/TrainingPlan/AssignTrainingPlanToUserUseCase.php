<?php

declare(strict_types=1);

namespace App\Application\UseCase\TrainingPlan;

use App\Domain\Exception\TrainingPlanNotFoundException;
use App\Domain\Exception\UserNotFoundException;
use App\Repository\TrainingPlanRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTrainingPlanRepositoryInterface;

class AssignTrainingPlanToUserUseCase
{
    public function __construct(
        private readonly UserTrainingPlanRepositoryInterface $userTrainingPlanRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly TrainingPlanRepositoryInterface $trainingPlanRepository
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws TrainingPlanNotFoundException
     */
    public function execute(int $userId, int $trainingPlanId): void
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException('User not found.');
        }

        $trainingPlan = $this->trainingPlanRepository->findById($trainingPlanId);
        if (!$trainingPlan) {
            throw new TrainingPlanNotFoundException('Training plan not found.');
        }

        if ($this->userTrainingPlanRepository->isAssigned($userId, $trainingPlanId)) {
            // Можно выбросить исключение или просто ничего не делать
            return;
        }

        $this->userTrainingPlanRepository->assign($userId, $trainingPlanId);
    }
}
