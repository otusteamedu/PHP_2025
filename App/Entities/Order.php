<?php

namespace App\Entities;

class Order
{
    private int $id;
    private int $userId;

    public function __construct(int $id, int $userId)
    {
        $this->id = $id;
        $this->setUserId($userId);
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

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}
