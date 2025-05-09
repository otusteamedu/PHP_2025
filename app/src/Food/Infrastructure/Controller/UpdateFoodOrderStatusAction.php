<?php

declare(strict_types=1);

namespace App\Food\Infrastructure\Controller;

use App\Food\Application\UseCase\UpdateOrderStatus\UpdateOrderStatusRequest;
use App\Food\Application\UseCase\UpdateOrderStatus\UpdateOrderStatusUseCase;
use App\Shared\Infrastructure\Controller\BaseAction;
use App\Shared\Infrastructure\Http\Request;

class UpdateFoodOrderStatusAction extends BaseAction
{
    public function __construct(private readonly UpdateOrderStatusUseCase $updateOrderStatusUseCase)
    {
    }

    public function __invoke(Request $request)
    {
        $orderId = $request->post('order_id');
        $newStatus = $request->post('status');
        $command = new UpdateOrderStatusRequest($orderId, $newStatus);
        $result = ($this->updateOrderStatusUseCase)($command);

        return $this->responseSuccess($result)->asJson();
    }
}
