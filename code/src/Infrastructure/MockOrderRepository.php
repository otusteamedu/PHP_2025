<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\OrderRepository;

class MockOrderRepository implements OrderRepository
{
    private array $orders = [
        '1' => ['sum' => 10.1, 'paid' => false],
        '2' => ['sum' => 20.5, 'paid' => false],
    ];

    public function setOrderIsPaid(string $orderNumber, float $sum): bool
    {
        if (!isset($this->orders[$orderNumber])) {
            throw new \Exception('Order not found');
        }

        if ($this->orders[$orderNumber]['sum'] !== $sum) {
            throw new \Exception('Incorrect order sum');
        }

        $this->orders[$orderNumber]['paid'] = true;
        
        // Для теста всегда возвращаем true
        return true;
    }
}
