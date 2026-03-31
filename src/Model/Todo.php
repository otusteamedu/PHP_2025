<?php

namespace App\Model;

class Todo {
    public function __construct(
        private int $id,
        private string $title,
        private bool $completed = false
    ) {}

    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function isCompleted(): bool { return $this->completed; }

    public function toArray(): array {
        return ['id' => $this->id, 'title' => $this->title, 'completed' => $this->completed];
    }
}
