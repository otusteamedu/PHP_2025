<?php

declare(strict_types=1);

namespace App\Application\UseCase\TrainingPlan\Dto;

class TrainingPlanWithExercisesByDayDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly array $days,
    ) {
    }
}
