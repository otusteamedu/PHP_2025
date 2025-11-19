<?php

namespace App\Handlers\Commands;

use App\Services\TelegramService;

abstract class BaseCommand implements CommandInterface
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    protected function getPriorityEmoji(string $priority): string
    {
        return match($priority) {
            'high' => '🔴',
            'medium' => '🟡',
            'low' => '🟢',
            default => '⚪'
        };
    }

    protected function getPriorityText(string $priority): string
    {
        return match($priority) {
            'high' => 'Высокий 🔴',
            'medium' => 'Средний 🟡',
            'low' => 'Низкий 🟢',
            default => 'Неизвестный'
        };
    }

    protected function getReminderTypeText(string $reminderType): string
    {
        return match($reminderType) {
            'one_time' => 'Однократное',
            'daily' => 'Ежедневное',
            'weekly' => 'Еженедельное',
            'monthly' => 'Ежемесячное',
            default => 'Без напоминания'
        };
    }
}