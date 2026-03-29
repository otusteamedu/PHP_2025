<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Actions;

use App\Application\DTO\CreateOrderRequest;
use App\Application\UseCases\CreateOrderUseCase;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Domain\Interfaces\ActionInterface;

class CreateOrderAction implements ActionInterface
{
    public function __construct(
        private CreateOrderUseCase $createOrderUseCase
    ) {}

    public function supports(Request $request): bool
    {
        return $request->isPost() && $request->getPath() === '/order';
    }

    public function handle(Request $request): Response
    {
        $body = $request->getBody();

        $orderRequest = CreateOrderRequest::fromArray($body);
        $result = $this->createOrderUseCase->execute($orderRequest);

        if ($result->success) {
            return Response::success($result->toArray());
        }

        return Response::error($result->message, 400);
    }
}
