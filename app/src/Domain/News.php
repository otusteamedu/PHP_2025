<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

class News
{
    private ?int $id = null;

    private DateTimeImmutable $createdAt;

    public function __construct(
        private string  $url,
        private ?string $title,
    )
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
