<?php

declare(strict_types=1);

namespace Otus\Queue\Domain\Entity;

final readonly class Message
{
    /**
     * @param string $author
     * @param string $text
     * @param int $createdAt
     */
    public function __construct(
        public string $author,
        public string $text,
        public int $createdAt,
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'author' => $this->author,
            'text' => $this->text,
            'created_at' => $this->createdAt,
        ];
    }
}
