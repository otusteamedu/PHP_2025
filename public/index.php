<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Services\TelegramService;
use App\Services\TaskService;
use App\Services\ReminderService;
use App\Services\AnalyticsService;
use App\Repositories\TaskRepository;
use App\Repositories\UserStateRepository;
use App\Handlers\CommandHandler;

try {
    Config::load();

    $telegramService = new TelegramService();
    $taskRepository = new TaskRepository();
    $taskService = new TaskService($taskRepository);
    $userStateRepository = new UserStateRepository();
    $reminderService = new ReminderService($taskRepository, $telegramService);
    $analyticsService = new AnalyticsService($taskRepository);
    $commandHandler = new CommandHandler(
        $telegramService, 
        $taskService, 
        $reminderService, 
        $userStateRepository,
        $analyticsService
    );

    $update = $telegramService->getTelegram()->getWebhookUpdate();
    
    if (Config::get('app.debug')) {
        error_log("Update received: " . json_encode($update));
    }
    
    $commandHandler->handle($update);
    
    http_response_code(200);
    echo "OK";
    
} catch (Exception $e) {
    error_log("Fatal error: " . $e->getMessage());
    http_response_code(200);
    echo "Error occurred";
}