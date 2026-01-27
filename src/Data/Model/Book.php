<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Data\Model;

class Book
{
    private string $title;
    private string $sku;
    private string $category;
    private int $price;
    private array $stock;

    public function __construct(string $title, string $sku, string $category, int $price, array $stock)
    {
        $this->title = $title;
        $this->sku = $sku;
        $this->category = $category;
        $this->price = $price;
        $this->stock = $stock;
    }

    public function getTitle():string
    {
        return $this->title;
    }

    public function getSku():string
    {
        return $this->sku;
    }

    public function getCategory():string
    {
        return $this->category;
    }

    public function getPrice():int
    {
        return $this->price;
    }

    public function getStock(): array
    {
        return $this->stock;
    }
}