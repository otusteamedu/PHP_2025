<?php

declare(strict_types=1);

namespace App\Domain\Entity;

readonly class TrainingPlanExercise
{
    public function __construct(
        private ?int     $id,
        private Exercise $exercise,
        private int      $sequence,
        private ?int     $repetitions,
        private ?int     $duration,
        private ?int     $cycle
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExercise(): Exercise
    {
        return $this->exercise;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getRepetitions(): ?int
    {
        return $this->repetitions;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function getCycle(): ?int
    {
        return $this->cycle;
    }
}
