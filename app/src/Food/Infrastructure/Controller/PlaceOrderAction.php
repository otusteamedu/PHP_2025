<?php

declare(strict_types=1);

namespace App\Food\Infrastructure\Controller;

use App\Food\Application\UseCase\PlaceOrder\PlaceOrderRequest;
use App\Food\Application\UseCase\PlaceOrder\PlaceOrderUseCase;
use App\Shared\Infrastructure\Controller\BaseAction;
use App\Shared\Infrastructure\Http\Request;

class PlaceOrderAction extends BaseAction
{
    public function __construct(private readonly PlaceOrderUseCase $placeOrderUseCase)
    {
    }

    public function __invoke(Request $request)
    {
        $command = new PlaceOrderRequest();
        $result = ($this->placeOrderUseCase)($command);

        return $this->responseSuccess($result)->asJson();
    }
}
