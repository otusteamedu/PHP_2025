<?php

namespace App\Handlers\Commands;

class UnknownCommand extends BaseCommand
{
    public function execute(int $chatId, int $userId, string $args = ''): void
    {
        $this->telegramService->sendMessage($chatId, 
            "🤔 Неизвестная команда. Используйте /start для просмотра доступных команд."
        );
    }
}