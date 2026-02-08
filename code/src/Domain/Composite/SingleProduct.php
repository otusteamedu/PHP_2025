<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Composite;

class SingleProduct implements OrderComponentInterface
{
    private string $status = 'new';

    public function __construct(private string $name, private float $price)
    {
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function moveStatus(string $status): string
    {
        $this->status = $status;
        return "Статус продукта '{$this->name}' изменен на '{$this->status}'";
    }
}
