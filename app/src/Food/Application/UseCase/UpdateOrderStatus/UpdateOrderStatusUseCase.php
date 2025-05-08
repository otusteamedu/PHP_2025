<?php
declare(strict_types=1);

namespace App\Food\Application\UseCase\UpdateOrderStatus;

use App\Food\Application\Service\FoodOrderStatusHandler\CancelFoodOrderStatusHandler;
use App\Food\Application\Service\FoodOrderStatusHandler\CompletedFoodOrderStatusHandler;
use App\Food\Application\Service\FoodOrderStatusHandler\EmptyFoodOrderHandler;
use App\Food\Application\Service\FoodOrderStatusHandler\FoodOrderStatusHandlerInterface;
use App\Food\Application\Service\FoodOrderStatusHandler\ReadyFoodOrderStatusHandler;
use App\Food\Application\Service\FoodOrderStatusValidator\FoodOrderStatusValidator;
use App\Food\Domain\Aggregate\Order\FoodOrderStatusType;
use App\Food\Domain\Repository\FoodOrderRepositoryInterface;
use App\Shared\Domain\Service\AssertService;

readonly class UpdateOrderStatusUseCase
{
    private FoodOrderStatusHandlerInterface $foodOrderStatusHandler;

    public function __construct(
        private FoodOrderRepositoryInterface $foodOrderRepository,
    )
    {
        $this->foodOrderStatusHandler = new EmptyFoodOrderHandler();
        $this->foodOrderStatusHandler
            ->setNext(new CancelFoodOrderStatusHandler())
            ->setNext(new CompletedFoodOrderStatusHandler())
            ->setNext(new ReadyFoodOrderStatusHandler());
    }

    public function __invoke(UpdateOrderStatusRequest $updateOrderStatusRequest): void
    {
        $order = $this->foodOrderRepository->findById($updateOrderStatusRequest->orderId);
        AssertService::notNull($order, message: "Order with id {$updateOrderStatusRequest->orderId} not found.");

        $newStatus = FoodOrderStatusType::tryFrom($updateOrderStatusRequest->newStatus);
        AssertService::notNull($newStatus, message: "Status {$updateOrderStatusRequest->newStatus} cannot be created.");

        $order->setStatus($newStatus);
        $validator = new FoodOrderStatusValidator($this->foodOrderStatusHandler);
        $validator->validate($order);
        $this->foodOrderRepository->add($order);
    }
}