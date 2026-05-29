<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\TelegramBot;

use MkdBot\Application\UseCase\HandleTelegramWebhook;
use MkdBot\Infrastructure\Interface\TelegramWebhookClientInterface;
use Psr\Log\LoggerInterface;
use Telegram\Bot\Objects\Update as UpdateObject;
use Throwable;

/**
 * Worker Long Polling для Telegram — долгоживущий процесс
 * Получает обновления через getUpdates() и передаёт их в HandleTelegramWebhook
 * Запускается через supervisor, поддерживает graceful shutdown через PCNTL-сигналы
 */
class TelegramLongPollWorker
{
    private bool $shouldStop = false;

    /** @var int Таймаут Long Polling запроса к Telegram API (сек) */
    private const POLL_TIMEOUT = 30;

    /** @var int Задержка при ошибке перед повторным запросом (сек) */
    private const ERROR_DELAY = 5;

    public function __construct(
        private readonly TelegramWebhookClientInterface $telegramBot,
        private readonly HandleTelegramWebhook $handleTelegramWebhook,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Запускает цикл Long Polling с graceful shutdown
     */
    public function run(): void
    {
        // Регистрация обработчиков сигналов для graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, function (): void {
                $this->logger->info("Telegram Long Polling: получен SIGTERM, завершение...");
                $this->shouldStop = true;
            });
            pcntl_signal(SIGINT, function (): void {
                $this->logger->info("Telegram Long Polling: получен SIGINT, завершение...");
                $this->shouldStop = true;
            });
            pcntl_async_signals(true);
        }

        $this->logger->info("Telegram Long Polling: worker запущен");

        // Удалить webhook если был установлен — иначе getUpdates не работает
        try {
            $this->telegramBot->deleteWebhook();
            $this->logger->info("Telegram Long Polling: webhook удалён (если был установлен)");
        } catch (Throwable $e) {
            $this->logger->warning("Telegram Long Polling: не удалось удалить webhook: " . $e->getMessage());
        }

        $offset = 0;

        while (!$this->shouldStop) {
            try {
                // Обработка сигналов перед запросом
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }

                if ($this->shouldStop) { // @phpstan-ignore if.alwaysFalse — проверка после dispatch
                    break;
                }

                $updates = $this->telegramBot->getUpdates([
                    'offset' => $offset,
                    'timeout' => self::POLL_TIMEOUT,
                ]);

                foreach ($updates as $update) {
                    $this->handleUpdate($update);
                    $offset = $update->get('update_id') + 1;
                }

                // Обработка сигналов после обработки обновлений
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }
            } catch (Throwable $e) {
                $this->logger->error("Telegram Long Polling: ошибка — " . $e->getMessage());
                // Задержка перед повторной попыткой — чтобы не спамить при постоянных ошибках
                $this->sleepWithSignalCheck(self::ERROR_DELAY);
            }
        }

        $this->logger->info("Telegram Long Polling: worker остановлен");
    }

    /**
     * Обрабатывает одно обновление от Telegram
     * Сериализует UpdateObject в JSON и передаёт в HandleTelegramWebhook
     */
    private function handleUpdate(UpdateObject $update): void
    {
        try {
            // HandleTelegramWebhook::execute() принимает строку (JSON body)
            // Сериализуем UpdateObject обратно в JSON
            $body = json_encode($update->toArray(), JSON_UNESCAPED_UNICODE);

            $this->logger->debug("Telegram Long Polling: обработка update_id=" . $update->get('update_id'));

            $this->handleTelegramWebhook->execute($body);
        } catch (Throwable $e) {
            $this->logger->error("Telegram Long Polling: ошибка обработки update: " . $e->getMessage());
        }
    }

    /**
     * Спит указанное количество секунд, проверяя сигналы каждые 100 мс
     * Позволяет быстро реагировать на SIGTERM/SIGINT во время сна
     */
    private function sleepWithSignalCheck(int $seconds): void
    {
        $elapsed = 0;
        while ($elapsed < $seconds && !$this->shouldStop) {
            usleep(100000); // 100 мс
            $elapsed += 0.1;

            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }
        }
    }
}
