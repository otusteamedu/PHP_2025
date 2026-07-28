<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Handler;

use App\Application\UseCases\GetTaskStatusUseCase;
use App\Infrastructure\Http\Exception\InvalidArgumentException;
use App\Infrastructure\Http\Request\Request;
use App\Infrastructure\Http\Response\JsonResponse;
use App\Infrastructure\Http\Validator\TaskNumberValidator;

final readonly class GetTaskStatusHandler implements HandlerInterface
{
    public function __construct(
        private GetTaskStatusUseCase $useCase,
    )
    {
    }

    public function handle(Request $request): JsonResponse
    {
        try {
            $number = TaskNumberValidator::validate(
                $request->getAttribute('number')
            );
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ],
                400);
        }

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
