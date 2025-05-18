<?php

declare(strict_types=1);

namespace App\News\Application\DTO;

class NewsDTO
{
    public ?string $id;
    public ?string $title;
    public ?string $link;
    public ?\DateTimeImmutable $created_at;
}
