<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Handler;

use App\Application\UseCases\CreateTaskUseCase;
use App\Infrastructure\Http\Request\Request;
use App\Infrastructure\Http\Response\JsonResponse;

final readonly class CreateTaskHandler implements HandlerInterface
{
    public function __construct(
        private CreateTaskUseCase $useCase,
    )
    {
    }

    public function handle(Request $request): JsonResponse
    {
        $task = $this->useCase->execute();

        return new JsonResponse(
            $task->toArray(),
            201,
        );
    }
}
