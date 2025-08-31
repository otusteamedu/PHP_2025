<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\CreateStatementRequestDTO;
use App\Application\DTO\StatementRequestResponseDTO;
use App\Application\Interface\QueueServiceInterface;
use App\Application\Interface\StatementRequestRepositoryInterface;
use App\Domain\Entity\BankStatementRequest;
use Exception;

final class CreateStatementRequestUseCase
{
    public function __construct(
        private StatementRequestRepositoryInterface $repository,
        private QueueServiceInterface $queueService
    ) {
    }

    /**
     * Выполняет создание запроса на банковскую выписку
     */
    public function execute(CreateStatementRequestDTO $dto): StatementRequestResponseDTO
    {
        try {
            $request = BankStatementRequest::create(
                id: $this->generateId(),
                dateRange: $dto->toDateRange(),
                telegramChatId: $dto->toTelegramChatId()
            );

            $this->repository->save($request);

            // отправляем в очередь для обработки
            $this->queueService->publish('statement_requests', [
                'requestId' => $request->id(),
                'startDate' => $dto->startDate(),
                'endDate' => $dto->endDate(),
                'telegramChatId' => $dto->telegramChatId()
            ]);

            return StatementRequestResponseDTO::fromEntity($request);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Генерирует уникальный идентификатор для запроса
     */
    private function generateId(): string
    {
        return uniqid('stmt_', true);
    }
}
