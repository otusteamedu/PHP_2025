<?php

require 'vendor/autoload.php';

use App\Services\QueueService;

// Ensure this script is run from the command line
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

echo "Worker started. Waiting for tasks...\n";

try {
    $queueService = new QueueService();

    // Define the callback function that processes each task
    $callback = function ($task) {
        echo "--------------------------------------------------\n";
        echo "Received task: " . json_encode($task, JSON_UNESCAPED_UNICODE) . "\n";
        
        if (isset($task['type']) && $task['type'] === 'statement_generation') {
            processStatementGeneration($task);
        } else {
            echo "Unknown task type: " . ($task['type'] ?? 'none') . "\n";
        }
        
        echo "Task processed.\n";
    };

    // Start consuming messages
    // This call blocks and waits for messages
    $queueService->consume($callback);

} catch (Exception $e) {
    echo "Error in worker: " . $e->getMessage() . "\n";
    // Keep the container running for a bit if it crashes immediately, useful for debugging
    sleep(10);
}

function processStatementGeneration(array $task): void
{
    echo "Processing statement generation for " . $task['email'] . "...\n";
    echo "Period: " . $task['date_from'] . " to " . $task['date_to'] . "\n";
    
    // Simulate long processing time (e.g., generating PDF, querying DB)
    $processingTime = rand(3, 10);
    sleep($processingTime);
    
    echo "Statement generated in {$processingTime} seconds.\n";
    
    // Simulate sending notification
    sendNotification($task['email'], "Your bank statement for {$task['date_from']} - {$task['date_to']} is ready.");
}

function sendNotification(string $email, string $message): void
{
    echo "SENDING NOTIFICATION -> To: {$email}, Message: {$message}\n";
    // In a real app, you would use a mailer service or Telegram API here
}
