<?php declare(strict_types=1);

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'news')]
class NewsRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    public string $url;

    #[ORM\Column(type: 'string')]
    public string $title;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt;
}
