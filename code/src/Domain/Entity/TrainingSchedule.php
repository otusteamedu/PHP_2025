<?php

declare(strict_types=1);

namespace App\Domain\Entity;

/**
 * Расписание тренировок.
 */
readonly class TrainingSchedule
{
    /**
     * @param int|null $id
     * @param int $dayOfWeek День недели (1 - Понедельник, 7 - Воскресенье).
     * @param string $time Время тренировки в формате HH:MM.
     */
    public function __construct(
        private ?int   $id,
        private int    $dayOfWeek,
        private string $time
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
}
