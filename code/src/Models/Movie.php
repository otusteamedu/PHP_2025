<?php

declare(strict_types=1);

namespace App\Models;

class Movie extends BaseModel
{
    private string $title;
    private int $duration;
    private ?string $description;

    public function __construct(string $title, int $duration, ?string $description = null, ?int $id = null)
    {
        parent::__construct($id);
        $this->title = $title;
        $this->duration = $duration;
        $this->description = $description;
    }

    public static function getTableName(): string
    {
        return 'movie';
    }

    protected static function getSelectColumns(): array
    {
        return ['id', 'title', 'duration', 'description'];
    }

    public static function fromArray(array $data): static
    {
        return new static(
            (string)($data['title'] ?? ''),
            (int)($data['duration'] ?? 0),
            array_key_exists('description', $data) ? (string)$data['description'] : null,
            isset($data['id']) ? (int)$data['id'] : null
        );
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'duration' => $this->duration,
            'description' => $this->description,
        ];
    }

    protected function getAttributes(): array
    {
        return [
            'title' => $this->title,
            'duration' => $this->duration,
            'description' => $this->description,
        ];
    }
}
