<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Interface\StatementRequestRepositoryInterface;
use App\Application\Interface\TelegramNotificationServiceInterface;
use App\Domain\Service\BankStatementService;
use Exception;
use InvalidArgumentException;

final class ProcessStatementRequestUseCase
{
    public function __construct(
        private StatementRequestRepositoryInterface $repository,
        private BankStatementService $statementService,
        private TelegramNotificationServiceInterface $telegramService
    ) {
    }

    /**
     * Выполняет обработку запроса на банковскую выписку
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

            $statementContent = $this->statementService->generateStatement($request);

            $request = $request->markAsCompleted($statementContent);
            $this->repository->save($request);

            $this->telegramService->sendMessage(
                $request->telegramChatId(),
                $this->formatTelegramMessage($request, $statementContent)
            );
        } catch (Exception $e) {
            $request = $request->markAsFailed($e->getMessage());
            $this->repository->save($request);

            $this->telegramService->sendMessage(
                $request->telegramChatId(),
                $this->formatErrorMessage($request, $e->getMessage())
            );

            throw $e;
        }
    }

    /**
     * Форматирует сообщение об успешной генерации выписки
     */
    private function formatTelegramMessage($request, string $statementContent): string
    {
        return "✅ <b>Банковская выписка готова!</b>\n\n" .
               "📅 Период: " . $request->dateRange()->format() . "\n" .
               "🆔 ID запроса: <code>{$request->id()}</code>\n\n" .
               "📋 Выписка:\n" .
               "<pre>" . htmlspecialchars($statementContent) . "</pre>";
    }

    /**
     * Форматирует сообщение об ошибке при генерации выписки
     */
    private function formatErrorMessage($request, string $error): string
    {
        return "❌ <b>Ошибка при генерации выписки</b>\n\n" .
               "📅 Период: " . $request->dateRange()->format() . "\n" .
               "🆔 ID запроса: <code>{$request->id()}</code>\n" .
               "🚨 Ошибка: " . htmlspecialchars($error);
    }
}
