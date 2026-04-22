<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\TrainingPlan;
use App\Repository\TrainingPlanRepositoryInterface;

class CreateTrainingPlanUseCase
{
    public function __construct(private readonly TrainingPlanRepositoryInterface $trainingPlanRepository)
    {
    }

    /**
     * @throws \Exception
     */
    public function execute(array $data): TrainingPlan
    {
        // 1. Создаем доменную сущность
        $trainingPlan = new TrainingPlan(
            null, // ID будет присвоен базой данных
            $data['name'],
            $data['description'] ?? null,
            new \DateTimeImmutable()
        );

        // 2. Сохраняем через репозиторий
        return $this->trainingPlanRepository->save($trainingPlan);
    }
}
