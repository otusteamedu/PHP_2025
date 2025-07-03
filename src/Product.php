<?php

namespace Elisad5791\Phpapp;

use DateTimeImmutable;
use InvalidArgumentException;

class Product
{
    private ?int $id;
    private string $title;
    private int $price;
    private DateTimeImmutable $createdAt;
    
    public function __construct(string $title, int $price)
    {
        $this->id = null;
        $this->title = $title;
        $this->price = $price;
        $this->createdAt = new DateTimeImmutable();
    }
    
    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getPrice(): int { return $this->price; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    
    public function setTitle(string $title): void
    {
        if (strlen($title) < 2) {
            throw new InvalidArgumentException('Title too short');
        }
        $this->title = $title;
    }
    
    public function setPrice(int $price): void
    {
        if ($price <= 0) {
            throw new InvalidArgumentException('Invalid price');
        }
        $this->price = $price;
    }
}