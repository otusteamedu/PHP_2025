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
     * @param int $dayOfWeek День недели (1 - Понедельник, 7 - Воскресенье).
     * @param string $time Время тренировки в формате HH:MM.
     * @param DateTimeImmutable $date Дата тренировки.
     * @param TrainingPlanExercise[] $exercises
     */
    public function __construct(
        private ?int   $id,
        private int    $dayOfWeek,
        private string $time,
        private DateTimeImmutable $date,
        private array $exercises = []
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfWeek(): int
    {
        return $this->dayOfWeek;
    }

    public function getTime(): string
    {
        return $this->time;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
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
