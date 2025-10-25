<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Repository\FileRequestRepository;
use App\Infrastructure\Queue\RabbitMQQueueService;
use App\Application\UseCase\ProcessRequestUseCase;
use App\Domain\Service\RequestProcessingService;
use App\Infrastructure\Logging\Logger;
use App\Infrastructure\Config\AppConfig;

AppConfig::load();

$logger = Logger::getInstance();
$logger->info('Starting Request Worker');

$logger->info('Waiting for RabbitMQ to be ready');
$maxAttempts = 30;
$attempt = 0;
$queueService = null;

while ($attempt < $maxAttempts) {
    try {
        $queueService = new RabbitMQQueueService();
        $logger->info('RabbitMQ connection established');
        break;
    } catch (Exception $e) {
        $attempt++;
        $logger->warning('RabbitMQ not ready yet', ['attempt' => $attempt, 'max_attempts' => $maxAttempts]);
        if ($attempt >= $maxAttempts) {
            $logger->error('Failed to connect to RabbitMQ after maximum attempts');
            exit(1);
        }
        sleep(2);
    }
}

try {
    $repository = new FileRequestRepository();
    $statementService = new RequestProcessingService();

    $processUseCase = new ProcessRequestUseCase($repository, $statementService);

    $callback = function (array $message) use ($processUseCase, $logger) {
        $logger->info('Processing message', ['message' => $message]);

        try {
            $requestId = $message['requestId'];
            $logger->info('Processing request', ['request_id' => $requestId]);

            $processUseCase->execute($requestId);

            $logger->info('Request processed successfully', ['request_id' => $requestId]);
        } catch (Exception $e) {
            $logger->error('Error processing request: ' . $e->getMessage(), ['request_id' => $requestId ?? 'unknown']);
            throw $e;
        }
    };

    $logger->info('Starting to consume messages');
    $queueService->consume('request_processing', $callback);
} catch (Exception $e) {
    $logger->error('Fatal error: ' . $e->getMessage());
    exit(1);
}
