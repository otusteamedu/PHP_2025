<?php

declare(strict_types=1);

namespace Queues\Presentation\Console;

use Queues\Domain\Entities\Statement;
use Queues\Application\Interfaces\QueueInterface;
use Queues\Application\UseCases\GenerateStatementUseCase;
use Queues\Application\UseCases\SendStatementUseCase;

class Worker
{
    private const LOOP_U_SLEEP = 1000;
    private const SIMULATION_SLEEP_MIN = 5;
    private const SIMULATION_SLEEP_MAX = 15;
    private const ERROR_PAUSE = 5;

    private bool $shouldStop = false;

    public function __construct(
        private readonly QueueInterface $queue,
        private readonly GenerateStatementUseCase $generator,
        private readonly SendStatementUseCase $sendEmail,
    ) {
        if (function_exists('pcntl_signal')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGTERM, fn() => $this->shouldStop = true);
            pcntl_signal(SIGINT, fn() => $this->shouldStop = true);
        }
    }

    public function run(): void
    {
        echo "Worker запущен. Ожидание сообщений...\n";

        while (!$this->shouldStop) {
            $payload = $this->queue->dequeue();

            if ($payload) {
                $this->process($payload);
            } elseif (!$this->shouldStop) {
                usleep(self::LOOP_U_SLEEP);
            }
        }

        echo "\nWorker остановлен\n";
    }

    private function process(string $payload): void
    {
        try {
            $statement = Statement::fromJson($payload);
            echo sprintf("[%s] Обработка %s для %s\n", date('Y-m-d H:i:s'), $statement->id, $statement->email);

            $this->waitLoop(random_int(self::SIMULATION_SLEEP_MIN, self::SIMULATION_SLEEP_MAX));

            if (!$this->shouldStop) {
                $statement = $this->generator->execute($statement);
                $this->sendEmail->execute($statement);
                echo sprintf("[%s] Успешно\n\n", date('Y-m-d H:i:s'));
            } else {
                echo "\nОбработка прервана: сообщение будет возвращено в очередь\n";
                $this->queue->enqueue($payload); // Но теперь у него поменяется порядок: что делать - хз?!?
            }
        } catch (\Throwable $e) {
            if (!$this->shouldStop) {
                echo sprintf(
                    "[%s] Ошибка: %s\nПауза %d сек\n\n",
                    date('Y-m-d H:i:s'),
                    $e->getMessage(),
                    self::ERROR_PAUSE
                );
                $this->waitLoop(self::ERROR_PAUSE);
            }
        }
    }

    private function waitLoop(int $waitSeconds): void
    {
        $loopCnt = $waitSeconds * 1000000 / self::LOOP_U_SLEEP;
        for ($i = 0; $i < $loopCnt && !$this->shouldStop; $i++) {
            usleep(self::LOOP_U_SLEEP);
        }
    }
}
