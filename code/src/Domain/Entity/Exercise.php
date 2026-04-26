<?php

declare(strict_types=1);

namespace App\Domain\Entity;

readonly class Exercise
{
    public function __construct(
        private ?int    $id,
        private string  $title,
        private ?string $description = null,
        private ?string $muscle_group = null,
        private ?int    $level = null,
        private ?int    $calories_per_minute = null,
        private ?string $link_to_video = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getMuscleGroup(): ?string
    {
        return $this->muscle_group;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function getCaloriesPerMinute(): ?int
    {
        return $this->calories_per_minute;
    }

    public function getLinkToVideo(): ?string
    {
        return $this->link_to_video;
    }
}
