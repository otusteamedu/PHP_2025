<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Composite;

class ComboProduct implements OrderComponentInterface
{
    private array $products = [];
    private string $status = 'new';

    public function __construct(private string $name)
    {
    }

    public function add(OrderComponentInterface $product): void
    {
        $this->products[] = $product;
    }

    public function getPrice(): float
    {
        $total = 0;
        foreach ($this->products as $product) {
            $total += $product->getPrice();
        }
        return $total * 0.9; // Скидка 10% на комбо
    }

    public function moveStatus(string $status): array
    {
        $this->status = $status;
        $messages = ["Статус комбо '{$this->name}' изменен на '{$this->status}'"];
        foreach ($this->products as $product) {
            $messages[] = $product->moveStatus($status);
        }
        return $messages;
    }
}
