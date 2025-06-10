<?php
declare(strict_types=1);

namespace Domain\Users\Entity;

use Domain\Users\ValueObject\Email;
use Domain\Users\ValueObject\Name;
use Domain\Users\ValueObject\Phone;

class User
{

    private ?int $id = null;

    public function __construct(
        private readonly Name $name,
        private readonly Email $email,
        private readonly Phone $phone)
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPhone(): Phone
    {
        return $this->phone;
    }
}