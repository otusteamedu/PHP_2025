<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\RequestStatusDTO;
use App\Application\Interface\RequestRepositoryInterface;
use InvalidArgumentException;

final class GetRequestStatusUseCase
{
    public function __construct(
        private RequestRepositoryInterface $repository
    ) {
    }

    /**
     * Получает статус запроса
     */
    public function execute(string $requestId): RequestStatusDTO
    {
        $request = $this->repository->findById($requestId);

        if (!$request) {
            throw new InvalidArgumentException("Request with ID {$requestId} not found");
        }

        return RequestStatusDTO::fromRequest($request);
    }
}
