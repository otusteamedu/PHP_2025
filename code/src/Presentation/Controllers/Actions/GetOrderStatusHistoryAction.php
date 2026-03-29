<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Actions;

use App\Application\UseCases\GetOrderStatusHistoryUseCase;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Domain\Interfaces\ActionInterface;

class GetOrderStatusHistoryAction implements ActionInterface
{
    public function __construct(
        private GetOrderStatusHistoryUseCase $useCase
    ) {}

    public function supports(Request $request): bool
    {
        return $request->isGet() && preg_match('#^/order/([a-zA-Z0-9\-]+)/history$#', $request->getPath());
    }

    public function handle(Request $request): Response
    {
        if (!preg_match('#^/order/([a-zA-Z0-9\-]+)/history$#', $request->getPath(), $matches)) {
            return Response::error('Неверный формат ID заказа', 400);
        }

        $orderId = $matches[1];
        $result = $this->useCase->execute($orderId);

        if ($result['success']) {
            return Response::success($result);
        }

        return Response::error($result['message'], 404);
    }
}
