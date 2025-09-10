<?php

namespace App\Entity;

class Movie
{
    public function __construct(
        private string $name,
        private \DateTime $dateRelease,
        private int $duration,
        private string $description,
        private ?int $id = null,
    )
    {
    }

    public function getDuration(): string
    {
        return $this->duration;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDateRelease(): \DateTime
    {
        return $this->dateRelease;
    }
}