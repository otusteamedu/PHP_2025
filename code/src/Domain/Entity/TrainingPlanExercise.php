<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class TrainingPlanExercise
{
    public function __construct(
        private readonly ?int $id,
        private readonly TrainingPlan $trainingPlan,
        private readonly Exercise $exercise,
        private readonly int $sequence,
        private readonly ?int $repetitions,
        private readonly ?int $duration,
        private readonly ?int $cycle
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrainingPlan(): TrainingPlan
    {
        return $this->trainingPlan;
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
