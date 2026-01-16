<?php

namespace Arlex2305k\PatternsDb;

class Movie
{
    private ?int $id;
    private string $title;
    private ?string $description;
    private int $durationMinutes;
    private ?string $genre;
	private ?float $rating;

    public function __construct(
        string $title,
        ?string $description = null,
        int $durationMinutes,
        ?string $genre = null,
        ?float $rating = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->durationMinutes = $durationMinutes;
        $this->genre = $genre;
        $this->rating = $rating;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDurationMinutes(): int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): void
    {
        $this->durationMinutes = $durationMinutes;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): void
    {
        $this->genre = $genre;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): void
    {
        $this->rating = $rating;
    }
}
