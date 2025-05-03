<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\PlaceOrder;

use App\Food\Domain\Aggregate\Order\FoodOrder;
use App\Food\Domain\Repository\FoodOrderRepositoryInterface;

class PlaceOrderUseCase
{
    public function __construct(
        private FoodOrderRepositoryInterface $foodOrderRepository,
    )
    {
    }

    public function __invoke(PlaceOrderRequest $makeBurgerRequest): PlaceOrderResponse
    {
        $order = new FoodOrder();
        $this->foodOrderRepository->add($order);

        return new PlaceOrderResponse($order->getId());
    }
}