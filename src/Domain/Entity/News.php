<?php declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Title;
use App\Domain\ValueObject\Url;

class News
{
    private ?int $id = null;

    public function __construct(
        private Url $url,
        private Title $title,
        private \DateTimeImmutable $createdAt,
    )
    {
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): Title {
        return $this->title;
    }

    public function getUrl(): Url {
        return $this->url;
    }

    public function getCreatedAt(): \DateTimeImmutable {
        return $this->createdAt;
    }
}
