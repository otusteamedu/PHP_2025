<?php
declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Products\ProductCollection;
use App\Domain\Entity\Sale\Cost\CostInterface;

class CreateOrderUseCase
{

    public function __construct(
        private readonly ProductCollection $products,
        private readonly CostInterface     $cost,
    )
    {
    }

    public function __invoke(): void
    {
        $arListNameProduct = $this->products->getListNameProduct();
        $price = $this->cost->getOrderAmount($this->products);

        echo 'В работу передан заказ,<br>';
        echo 'нужно приготовить "' . implode(', ', $arListNameProduct) . '"<br>';
        echo 'Стоимость заказа ' . $this->cost->getMessagePrices() . '(' . $price . ')<br><br>';
    }
}