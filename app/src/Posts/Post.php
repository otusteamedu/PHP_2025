<?php
declare(strict_types=1);

namespace App\Posts;

final readonly class Post
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $title,
        public string $body,
    ) {}

    public static function new(int $userId, string $title, string $body): self
    {
        return new self(0, $userId, $title, $body);
    }

    public function withId(int $id): self
    {
        return new self($id, $this->userId, $this->title, $this->body);
    }

    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['user_id'],
            (string) $row['title'],
            (string) $row['body']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'title' => $this->title,
            'body' => $this->body,
        ];
    }
}
