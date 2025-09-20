<?php

declare(strict_types=1);


namespace Dinargab\Homework12\Model;

use InvalidArgumentException;

class Movie
{
    private string $title = "";
    private string $overview = "";
    private ?\DateTime $releaseDate = null;
    private int $duration = 0;
    private float $rating = 0.0;
    private ?int $id = null;


    public function __construct(string $title, string $overview, \DateTime $releaseDate, int $duration, float $rating, ?int $id = null)
    {
        if (!empty(trim($title))) {
            $this->title = trim($title);
        }
        if (!empty(trim($overview))) {
            $this->overview = trim($overview);
        }
        if (!empty($releaseDate)) {
            $this->releaseDate = $releaseDate;
        } else {
            $this->releaseDate = new \DateTime();
        }
        if (!empty($duration)) {
            $this->duration = $duration;
        }
        if (!empty($rating)) {
            $this->rating = $rating;
        }
        if (!empty($id)) {
            $this->id = $id;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOverview(): string
    {
        return $this->overview;
    }

    public function setOverview(string $overview): Movie
    {
        if ($overview) {
            $this->overview = $overview;
        }
        return $this;
    }

    public function getReleaseDate(): \DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTime $date): Movie
    {
        if ($date) {
            $this->releaseDate = $date;
        }
        return $this;
    }

    public function getDuration($formatted = false): int | string
    {
        if (!$formatted) {
            return $this->duration;
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        return sprintf("%d hours, %02d minutes", $hours, $minutes);
    }

    public function setDuration(int $duration): Movie
    {
        if ($duration < 0) {
            throw new InvalidArgumentException("Duration must be greater than zerp", 1);
        }
        if ($duration > 0) {
            $this->duration = $duration;
        }
        return $this;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): Movie
    {
        if ($rating < 0 || $rating > 10.0) {
            throw new InvalidArgumentException("Rating can only be in range of 0 to 10");
        }
        if ($rating) {
            $this->rating = $rating;
        }
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Movie
    {
        if ($title) {
            $this->title = $title;
        }
        return $this;
    }
}
