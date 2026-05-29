<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use Psr\Log\LoggerInterface;

/**
 * Обработка webhook от Telegram (v1 — минимальная: только логирование)
 */
class HandleTelegramWebhook
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Логирует получение webhook от Telegram
     */
    public function execute(string $body): void
    {
        $this->logger->debug("Получен webhook от Telegram: " . mb_substr($body, 0, 500));
    }
}
