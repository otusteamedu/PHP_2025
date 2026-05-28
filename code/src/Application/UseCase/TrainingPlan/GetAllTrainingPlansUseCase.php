<?php

declare(strict_types=1);

namespace App\Application\UseCase\TrainingPlan;

use App\Application\DTO\PaginationDTO;
use App\Domain\Repository\TrainingPlanRepositoryInterface;


readonly class GetAllTrainingPlansUseCase
{
    public function __construct(
        private TrainingPlanRepositoryInterface $trainingPlanRepository
    ) {}

    /**
     * Retrieves all training plans.
     *
     * @param int $page
     * @param int $limit
     * @return PaginationDTO
     */
    public function execute(int $page = 1, int $limit = 10): PaginationDTO
    {
        return $this->trainingPlanRepository->findAll($page, $limit);
    }
}
