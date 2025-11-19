<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Services\ReminderService;
use App\Services\TelegramService;
use App\Repositories\TaskRepository;

try {
    //устанавливаем часовой пояс
    date_default_timezone_set('Europe/Moscow');
    
    echo "🔔 Starting reminder service...\n";
    echo "🕐 Current time: " . date('Y-m-d H:i:s') . "\n";
    echo "🌐 Timezone: " . date_default_timezone_get() . "\n";
    
    Config::load();
    
    $telegramService = new TelegramService();
    $taskRepository = new TaskRepository();
    $reminderService = new ReminderService($taskRepository, $telegramService);
    
    // Отправляем напоминания
    $reminderService->sendDueReminders();
    
    echo "✅ Reminder service completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}