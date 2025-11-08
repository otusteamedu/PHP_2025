<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Builder\NewsBuilder;
use App\Domain\ValueObject\Author;
use App\Domain\ValueObject\Content;
use App\Domain\ValueObject\Title;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "news")]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'news')]
    private Category $category;

    #[ORM\Embedded(class: Title::class, columnPrefix: false)]
    private Title $title;

    #[ORM\Embedded(class: Author::class, columnPrefix: false)]
    private Author $author;

    #[ORM\Embedded(class: Content::class, columnPrefix: false)]
    private Content $content;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $created_at;

    public function __construct(NewsBuilder $builder)
    {
        $this->title = $builder->getTitle();
        $this->author = $builder->getAuthor();
        $this->category = $builder->getCategory();
        $this->content = $builder->getContent();
        $this->created_at = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }
}
