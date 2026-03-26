<?php

declare(strict_types=1);

namespace Api\Presentation\Console;

use Api\Domain\Interfaces\QueueInterface;
use Api\Application\UseCases\ProcessRequestUseCase;
use Monolog\Logger;

final class Worker
{
    private const LOOP_U_SLEEP = 1000;

    private bool $shouldStop = false;

    public function __construct(
        private QueueInterface $queue,
        private ProcessRequestUseCase $processUseCase,
        private Logger $logger
    ) {
        if (function_exists('pcntl_signal')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGTERM, fn() => $this->shouldStop = true);
            pcntl_signal(SIGINT, fn() => $this->shouldStop = true);
        }
    }

    public function run(): void
    {
        $this->logger->info('Worker запущен. Ожидание запросов...');
        echo "Worker запущен. Ожидание запросов...\n";

        while (!$this->shouldStop) {
            try {
                $message = $this->queue->dequeue();

                if ($message === null) {
                    usleep(self::LOOP_U_SLEEP);
                    continue;
                }

                $id = $message['id'] ?? null;

                if ($id === null) {
                    $this->logger->warning('Получен запрос без ID', ['message' => $message]);
                    continue;
                }

                $this->logger->info('Обработка запроса', ['id' => $id]);
                echo "Обработка запроса #{$id}...\n";

                $success = $this->processUseCase->execute((int)$id);

                if ($success) {
                    $this->logger->info('Запрос успешно обработан', ['id' => $id]);
                    echo "Запрос #{$id} успешно обработан\n";
                } else {
                    $this->logger->error('Ошибка обработки запроса', ['id' => $id]);
                    echo "Запрос #{$id} обработан с ошибкой\n";
                }
            } catch (\Throwable $e) {
                $this->logger->error('Ошибка в Worker', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                echo "Ошибка: {$e->getMessage()}\n";
                usleep(self::LOOP_U_SLEEP);
            }
        }

        echo "\nWorker остановлен\n";
    }
}
