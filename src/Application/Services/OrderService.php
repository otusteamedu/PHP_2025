<?php

namespace App\Application\Services;

use App\Domain\Entities\Order;
use App\Infrastructure\Builders\OrderBuilder;

class OrderService
{
    public function __construct(
        private OrderBuilder $orderBuilder
    ) {}

    public function createOrder(array $items, ?string $customerEmail = null): Order
    {
        $builder = $this->orderBuilder->createOrder();
        
        foreach ($items as $item) {
            $builder->addProduct(
                $item['type'],
                $item['ingredients'] ?? []
            );
        }
        
        if ($customerEmail) {
            $builder->setCustomerEmail($customerEmail);
        }
        
        return $builder->getOrder();
    }
}