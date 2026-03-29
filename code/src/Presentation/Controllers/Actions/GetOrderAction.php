<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Actions;

use App\Application\UseCases\GetOrderUseCase;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Domain\Interfaces\ActionInterface;

class GetOrderAction implements ActionInterface
{
    public function __construct(
        private GetOrderUseCase $getOrderUseCase
    ) {}

    public function supports(Request $request): bool
    {
        return $request->isGet() && preg_match('#^/order/([a-zA-Z0-9\-]+)$#', $request->getPath());
    }

    public function handle(Request $request): Response
    {
        if (!preg_match('#^/order/([a-zA-Z0-9\-]+)$#', $request->getPath(), $matches)) {
            return Response::error('Неверный формат ID заказа', 400);
        }

        $orderId = $matches[1];
        $result = $this->getOrderUseCase->execute($orderId);

        if ($result->success) {
            return Response::success($result->toArray());
        }

        return Response::error($result->message, 404);
    }
}
