<?php declare(strict_types=1);

namespace App\Entity;

class Order
{
    private ?int $id;
    private int $userId;
    private float $totalAmount;
    private \DateTimeInterface $createdAt;

    public function __construct(int $userId, float $totalAmount, \DateTimeInterface $createdAt, ?int $id = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->totalAmount = $totalAmount;
        $this->createdAt = $createdAt;
    }

    public static function createNew(int $userId, float $totalAmount): self
    {
        return new self($userId, $totalAmount, new \DateTimeImmutable(), null);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
