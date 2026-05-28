<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;

/**
 * Расписание тренировок.
 */
class TrainingSchedule
{
    /**
     * @param int|null $id
     * @param int $trainingPlanId
     * @param DateTimeImmutable|string $startTime
     * @param DateTimeImmutable $endTixme
     * @param TrainingPlanExercise[] $exercises
     */
    public function __construct(
        private readonly ?int                     $id,
        private readonly int                      $trainingPlanId,
        private readonly DateTimeImmutable|string $startTime,
        private readonly DateTimeImmutable        $endTime,
        private array                             $exercises = []
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrainingPlanId(): int
    {
        return $this->trainingPlanId;
    }

    public function getStartTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTimeImmutable
    {
        return $this->endTime;
    }

    public function getDate(): DateTimeImmutable
    {
        // Дата извлекается из времени начала
        return $this->startTime;
    }

    /**
     * @return TrainingPlanExercise[]
     */
    public function getExercises(): array
    {
        return $this->exercises;
    }

    /**
     * @param TrainingPlanExercise[] $exercises
     */
    public function setExercises(array $exercises): void
    {
        $this->exercises = $exercises;
    }
}
