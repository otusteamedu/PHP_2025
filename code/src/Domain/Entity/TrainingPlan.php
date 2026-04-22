<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class TrainingPlan
{
    /**
     * @param TrainingPlanExercise[] $exercises
     */
    public function __construct(
        private ?int $id,
        private string $name,
        private string $status,
        private ?string $description,
        private \DateTimeImmutable $created_at,
        private array $exercises = []
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
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
