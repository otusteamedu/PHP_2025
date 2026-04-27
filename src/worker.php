<?php

require 'vendor/autoload.php';

use App\Services\QueueService;
use App\Services\RequestStatusService;

// Убеждаемся, что скрипт запускается только из командной строки
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

echo "Worker started. Waiting for tasks...\n";

try {
    $queueService = new QueueService();
    $statusService = new RequestStatusService();

    // Определяем callback-функцию для обработки каждой задачи
    $callback = function ($task) use ($statusService) {
        echo "--------------------------------------------------\n";
        echo "Received task: " . json_encode($task, JSON_UNESCAPED_UNICODE) . "\n";

        $requestId = $task['request_id'] ?? null;

        if (!$requestId) {
            echo "Task skipped: missing request_id\n";
            return;
        }

        $statusService->markProcessing($requestId);

        try {
            if (($task['type'] ?? '') !== 'statement_generation') {
                throw new RuntimeException('Unknown task type: ' . ($task['type'] ?? 'none'));
            }

            $result = processStatementGeneration($task);
            $statusService->markCompleted($requestId, $result);
            echo "Task {$requestId} processed.\n";
        } catch (Throwable $e) {
            $statusService->markFailed($requestId, $e->getMessage());
            echo "Task {$requestId} failed: {$e->getMessage()}\n";
            throw $e;
        }
    };

    // Запускаем потребление сообщений
    // Этот вызов блокирует выполнение и ждет новые сообщения
    $queueService->consume($callback);

} catch (Exception $e) {
    echo "Error in worker: " . $e->getMessage() . "\n";
    // Немного удерживаем контейнер запущенным при мгновенном падении, это удобно для отладки
    sleep(10);
}

function processStatementGeneration(array $task): array
{
    if (empty($task['email']) || empty($task['date_from']) || empty($task['date_to'])) {
        throw new InvalidArgumentException('Task payload is missing required fields.');
    }

    echo "Processing statement generation for " . $task['email'] . "...\n";
    echo "Period: " . $task['date_from'] . " to " . $task['date_to'] . "\n";
    
    // Имитируем длительную обработку (например, генерацию PDF или запросы к БД)
    $processingTime = rand(3, 10);
    sleep($processingTime);
    
    echo "Statement generated in {$processingTime} seconds.\n";

    // Имитируем отправку уведомления
    $message = "Your bank statement for {$task['date_from']} - {$task['date_to']} is ready.";
    sendNotification($task['email'], $message);

    return [
        'message' => 'Statement generated successfully.',
        'processing_time_seconds' => $processingTime,
        'notified_email' => $task['email'],
        'finished_at' => gmdate('c'),
    ];
}

function sendNotification(string $email, string $message): void
{
    echo "SENDING NOTIFICATION -> To: {$email}, Message: {$message}\n";
    // В реальном приложении здесь обычно используется почтовый сервис или Telegram API
}
