<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Actions;

use App\Application\UseCases\CancelOrderUseCase;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Domain\Interfaces\ActionInterface;

class CancelOrderAction implements ActionInterface
{
    public function __construct(
        private CancelOrderUseCase $cancelOrderUseCase
    ) {}

    public function supports(Request $request): bool
    {
        return $request->isPost() && preg_match('#^/order/([a-zA-Z0-9\-]+)/cancel$#', $request->getPath());
    }

    public function handle(Request $request): Response
    {
        if (!preg_match('#^/order/([a-zA-Z0-9\-]+)/cancel$#', $request->getPath(), $matches)) {
            return Response::error('Неверный формат ID заказа', 400);
        }

        $orderId = $matches[1];
        $result = $this->cancelOrderUseCase->execute($orderId);

        if ($result->success) {
            return Response::success($result->toArray());
        }

        return Response::error($result->message, 400);
    }
}
