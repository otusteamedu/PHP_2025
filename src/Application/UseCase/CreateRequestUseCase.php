<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\CreateRequestDTO;
use App\Application\DTO\RequestResponseDTO;
use App\Application\Interface\QueueServiceInterface;
use App\Application\Interface\RequestRepositoryInterface;
use App\Domain\Entity\Request;
use App\Domain\ValueObject\Priority;
use App\Domain\ValueObject\RequestId;

final class CreateRequestUseCase
{
    public function __construct(
        private RequestRepositoryInterface $repository,
        private QueueServiceInterface $queueService
    ) {
    }

    /**
     * Создает новый запрос
     */
    public function execute(CreateRequestDTO $dto): RequestResponseDTO
    {
        $requestId = RequestId::generate();
        $priority = new Priority($dto->priority);

        $request = Request::create(
            $requestId,
            $dto->data,
            $priority
        );

        $this->repository->save($request);

        // публикуем сообщение в очередь
        $message = [
            'requestId' => $request->id()->value(),
            'data' => $request->data(),
            'priority' => $request->priority()->value(),
            'createdAt' => $request->createdAt()->format('c'),
        ];

        $this->queueService->publish('request_processing', $message);

        return RequestResponseDTO::fromRequest($request);
    }
}
