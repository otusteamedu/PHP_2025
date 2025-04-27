<?php


class Product
{
    private float $price;
    private string $name;
    private int $id;

    public function __construct(
        int $id,
        string $name,
        float $price
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
