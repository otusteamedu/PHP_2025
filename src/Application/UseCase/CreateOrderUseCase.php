<?php

namespace Blarkinov\Hw1500\Application\UseCase;

use Blarkinov\Hw1500\Application\Composite\Order;
use Blarkinov\Hw1500\Application\Factory\FoodCustomFabric;
use Blarkinov\Hw1500\Application\Factory\FoodFabric;
use Blarkinov\Hw1500\Application\Repository\Food\FoodRepository;
use Blarkinov\Hw1500\Application\Strategies\BaseCookingStrategy;
use Blarkinov\Hw1500\Application\Strategies\CustomCookingStrategy;
use Blarkinov\Hw1500\Application\UseCase\Request\NewOrderRequestDto;
use Blarkinov\Hw1500\Domain\Observer\OrderStatusEvent;
use Blarkinov\Hw1500\Domain\Observer\OrderStatusObserverInterface;
use Blarkinov\Hw1500\Infrastructure\Gateway\FileDataBase;

class CreateOrderUseCase
{

    public function __construct(private  CreateProductUseCase $createProduct) {}

    public function create(
        NewOrderRequestDto $newOrderDto,
        OrderStatusObserverInterface $observer,
    ):int {

        $foodRepostitory = new FoodRepository;

        foreach ($newOrderDto->getOrderFood() as $food) {
            if (strripos($food, 'custom') === false) {
                $foodObject = $this->createProduct->create($food, new FoodFabric, new BaseCookingStrategy);
                while (is_null($foodObject))
                    $foodObject = $this->createProduct->create($food, new FoodFabric, new BaseCookingStrategy);
                $foodRepostitory->save($foodObject);
            } else {
                $foodObject = $this->createProduct->create($food, new FoodCustomFabric, new CustomCookingStrategy);
                while (is_null($foodObject))
                    $foodObject = $this->createProduct->create($food, new FoodCustomFabric, new CustomCookingStrategy);
                $foodRepostitory->save($foodObject);
            }
        }

        $db = new FileDataBase;

        $order = new Order($foodRepostitory, $db);

        $order->calculate();

        $id = (new FileDataBase)->setOrder($order);

        $observer->notify(new OrderStatusEvent($order->getId(), $order->getStatus()));

        return $id;
    }
}
