<?php
declare(strict_types=1);


namespace App\News\Domain\Entity;

use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use Ramsey\Uuid\Uuid;

class News
{
    private string $id;
    private \DateTimeImmutable $createdAt;

    public function __construct(
        private readonly NewsTitle $title,
        private readonly NewsLink  $link,
    )
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): string
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