<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\CategoryName;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "categories")]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Embedded(class: CategoryName::class, columnPrefix: false)]
    private CategoryName $name;

    public function __construct(CategoryName $name)
    {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): CategoryName
    {
        return $this->name;
    }
}
