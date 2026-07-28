<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Handler;

use App\Application\UseCases\GetTaskStatusUseCase;
use App\Infrastructure\Http\Request\Request;
use App\Infrastructure\Http\Response\JsonResponse;

final readonly class GetTaskStatusHandler implements HandlerInterface
{
    public function __construct(
        private GetTaskStatusUseCase $useCase,
    )
    {
    }

    public function handle(Request $request): JsonResponse
    {
        $number = $request->getAttribute('number');

        $status = $this->useCase->execute((int)$number);

        if ($status === null) {
            return new JsonResponse([
                'message' => "Задача не найдена",
            ],
            404);
        }

        return new JsonResponse([
            'message' => $status,
            'number' => $number,
        ]);
    }
}
