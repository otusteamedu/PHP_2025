<?php

declare(strict_types=1);

namespace App\Application\Interface;

use App\Domain\ValueObject\TelegramChatId;

interface TelegramNotificationServiceInterface
{
    /**
     * Отправляет сообщение в Telegram чат
     */
    public function sendMessage(TelegramChatId $chatId, string $message): void;
}
