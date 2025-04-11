<?php

declare(strict_types=1);

namespace App\Entities;

use App\Proxy\OrderProxy;

class User
{
    private int $id;

    private string $firstName;

    private string $lastName;

    private string $email;

    private OrderProxy $orderProxy;

    public function __construct(
        int    $id,
        string $firstName,
        string $lastName,
        string $email
    )
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOrders(): array
    {
        $this->orderProxy = new OrderProxy();

        return $this->orderProxy->getOrdersByUser($this->id);
    }
}
