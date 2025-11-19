<?php

namespace App\Handlers\Commands;

use App\Services\AnalyticsService;
use App\Models\Task;
use App\Services\TelegramService;

class StatsCommand extends BaseCommand
{
    private AnalyticsService $analyticsService;

    public function __construct(
        TelegramService $telegramService,
        AnalyticsService $analyticsService
    ) {
        parent::__construct($telegramService);
        $this->analyticsService = $analyticsService;
    }

    public function execute(int $chatId, int $userId, string $args = ''): void
    {
        try {
            $statistics = $this->analyticsService->getUserStatistics($userId);
            $message = $this->formatStatisticsMessage($statistics);
            $this->telegramService->sendMessage($chatId, $message);
        } catch (\Exception $e) {
            error_log("Error generating statistics: " . $e->getMessage());
            $this->telegramService->sendMessage($chatId, "❌ Ошибка при получении статистики.");
        }
    }

    private function formatStatisticsMessage(array $statistics): string
    {
        $basic = $statistics['basic'];
        $priority = $statistics['priority'];
        $dueDates = $statistics['due_dates'];
        $trend = $statistics['completion_trend'];

        $message = "📊 СТАТИСТИКА ЗАДАЧ\n\n";
        
        // Базовая статистика
        $message .= "📈 Общая статистика:\n";
        $message .= "• Всего задач: {$basic['total']}\n";
        $message .= "• ✅ Выполнено: {$basic['completed']}\n";
        $message .= "• ⏳ В процессе: {$basic['pending']}\n";
        $message .= "• 📊 Процент выполнения: {$basic['completion_rate']}%\n\n";
        
        // Статистика по приоритетам
        $message .= "⚡ По приоритетам:\n";
        $message .= "• 🔴 Высокий: {$priority[Task::PRIORITY_HIGH]['completed']}/{$priority[Task::PRIORITY_HIGH]['total']} выполнено\n";
        $message .= "• 🟡 Средний: {$priority[Task::PRIORITY_MEDIUM]['completed']}/{$priority[Task::PRIORITY_MEDIUM]['total']} выполнено\n";
        $message .= "• 🟢 Низкий: {$priority[Task::PRIORITY_LOW]['completed']}/{$priority[Task::PRIORITY_LOW]['total']} выполнено\n\n";
        
        // Статистика по срокам
        $message .= "📅 По срокам выполнения:\n";
        $message .= "• 🔴 Просрочено: {$dueDates['overdue']}\n";
        $message .= "• 🟡 На сегодня: {$dueDates['today']}\n";
        $message .= "• 🟢 На будущее: {$dueDates['future']}\n";
        $message .= "• ⚪ Без срока: {$dueDates['no_due_date']}\n\n";
        
        // Тренд выполнения
        $message .= "📈 Активность за 7 дней:\n";
        $completedLast7Days = array_sum($trend);
        $message .= "• Выполнено за неделю: {$completedLast7Days} задач\n";
        
        // Покажем самый продуктивный день
        if ($completedLast7Days > 0) {
            $maxDay = array_search(max($trend), $trend);
            $maxTasks = $trend[$maxDay];
            $formattedDate = date('d.m', strtotime($maxDay));
            $message .= "• 🏆 Самый продуктивный день: {$formattedDate} ({$maxTasks} задач)\n";
        }

        return $message;
    }
}