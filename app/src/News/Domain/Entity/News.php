<?php

declare(strict_types=1);

namespace App\News\Domain\Entity;

use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use Symfony\Component\Uid\Uuid;

class News
{
    private \DateTimeImmutable $createdAt;

    public function __construct(
        private readonly Uuid      $id,
        private readonly NewsTitle $title,
        private readonly NewsLink  $link,
    )
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTitle(): NewsTitle
    {
        return $this->title;
    }

    public function getLink(): NewsLink
    {
        return $this->link;
    }

}
