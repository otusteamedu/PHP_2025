<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Email;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "subscriptions")]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Embedded(class: Email::class, columnPrefix: false)]
    private Email $email;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'subscriptions')]
    private Category $category;

    public function __construct(Category $category, Email $email)
    {
        $this->category = $category;
        $this->email = $email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}
