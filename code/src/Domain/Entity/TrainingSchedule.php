<?php

declare(strict_types=1);

namespace App\Domain\Entity;

/**
 * Расписание тренировок.
 */
class TrainingSchedule
{
    /**
     * @param int|null $id
     * @param int $dayOfWeek День недели (1 - Понедельник, 7 - Воскресенье).
     * @param string $time Время тренировки в формате HH:MM.
     */
    public function __construct(
        private readonly ?int $id,
        private readonly int $dayOfWeek,
        private readonly string $time
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
