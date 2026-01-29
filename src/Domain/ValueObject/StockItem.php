<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Domain\ValueObject;

use InvalidArgumentException;

class StockItem
{
    private string $shop;
    private int $stock;

    public function __construct(string $shop, int $stock)
    {
        if ($stock < 0) {
            throw new InvalidArgumentException('Stock must be positive');
        }
        $this->shop  = $shop;
        $this->stock = $stock;
    }

    public function getShop(): string
    {
        return $this->shop;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function toArray(): array
    {
        return [
            'shop'  => $this->shop,
            'stock' => $this->stock,
        ];
    }

    public static function fromArray(array $data): StockItem
    {
        return new self($data['shop'], $data['stock']);
    }

}