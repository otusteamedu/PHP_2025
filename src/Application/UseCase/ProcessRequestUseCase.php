<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Interface\RequestRepositoryInterface;
use App\Domain\Service\RequestProcessingService;
use Exception;
use InvalidArgumentException;

final class ProcessRequestUseCase
{
    public function __construct(
        private RequestRepositoryInterface $repository,
        private RequestProcessingService $processingService
    ) {
    }

    /**
     * Выполняет обработку запроса
     */
    public function execute(string $requestId): void
    {
        $request = $this->repository->findById($requestId);

        if (!$request) {
            throw new InvalidArgumentException("Request with ID {$requestId} not found");
        }

        if (!$request->isPending()) {
            throw new InvalidArgumentException("Request {$requestId} is not pending");
        }

        try {
            $request = $request->markAsProcessing();
            $this->repository->save($request);

            $result = $this->processingService->processRequest($request);

            $request = $request->markAsCompleted($result);
            $this->repository->save($request);
        } catch (Exception $e) {
            $request = $request->markAsFailed($e->getMessage());
            $this->repository->save($request);

            throw $e;
        }
    }
}
