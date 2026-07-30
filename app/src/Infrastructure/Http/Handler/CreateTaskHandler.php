<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Handler;

use App\Application\UseCases\AddTaskToQueueUseCase;
use App\Application\UseCases\CreateTaskUseCase;
use App\Infrastructure\Http\Request\Request;
use App\Infrastructure\Http\Response\JsonResponse;

final readonly class CreateTaskHandler implements HandlerInterface
{
    public function __construct(
        private CreateTaskUseCase $createTaskUseCase,
        private AddTaskToQueueUseCase $addTaskToQueueUseCase,
    )
    {
    }

    public function handle(Request $request): JsonResponse
    {
        $task = $this->createTaskUseCase->execute($request->getJsonBody());

        $this->addTaskToQueueUseCase->execute($task);

        return new JsonResponse(
            $task->toArray(),
            202,
            headers: [
                'Location' => '/tasks/' . $task->getNumber(),
            ],
        );
    }
}
