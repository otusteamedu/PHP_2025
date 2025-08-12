<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\ORM\Entity;

use App\Infrastructure\Persistence\Doctrine\ORM\Repository\NewsRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'news')]
#[ORM\Entity(repositoryClass: NewsRepository::class)]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer', unique: true)]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private string $url;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private string $title;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private ?\DateTimeImmutable $createdAt;

    public function __construct(
        string $url,
        string $title,
    )
    {
        $this->url = $url;
        $this->title = $title;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
