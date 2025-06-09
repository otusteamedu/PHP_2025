<?php

declare(strict_types=1);

namespace App\Domain\Event;

use DateTimeImmutable;

readonly class NewsIsCreatedEvent
{
    public function __construct(
        public int $id,
        public string $title,
        public string $author,
        public int $category_id,
        public string $content,
        public DateTimeImmutable $created_at,
    )
    {
    }
}
