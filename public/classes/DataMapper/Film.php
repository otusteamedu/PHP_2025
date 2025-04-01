<?php declare(strict_types=1);

namespace classes\DataMapper;

class Film
{
    private int $id;

    private string $title;

    private string $code;

    private int $rating;

    public function __construct(
        int $id,
        string $title,
        string $code,
        int $rating
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->code = $code;
        $this->rating = $rating;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;
        return $this;
    }
}