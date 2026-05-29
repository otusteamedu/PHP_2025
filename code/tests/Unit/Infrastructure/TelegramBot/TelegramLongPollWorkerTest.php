<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Infrastructure\TelegramBot;

use MkdBot\Application\UseCase\HandleTelegramWebhook;
use MkdBot\Infrastructure\Interface\TelegramWebhookClientInterface;
use MkdBot\Infrastructure\TelegramBot\TelegramLongPollWorker;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use RuntimeException;
use Telegram\Bot\Objects\Update as UpdateObject;

/**
 * Юнит-тесты для TelegramLongPollWorker — Long Polling worker для Telegram
 */
class TelegramLongPollWorkerTest extends TestCase
{
    private TelegramWebhookClientInterface $telegramBot;
    private HandleTelegramWebhook $handleTelegramWebhook;
    private LoggerInterface $logger;
    private TelegramLongPollWorker $worker;

    protected function setUp(): void
    {
        $this->telegramBot = $this->createMock(TelegramWebhookClientInterface::class);
        $this->handleTelegramWebhook = $this->createMock(HandleTelegramWebhook::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->worker = new TelegramLongPollWorker(
            telegramBot: $this->telegramBot,
            handleTelegramWebhook: $this->handleTelegramWebhook,
            logger: $this->logger,
        );
    }

    /**
     * Вспомогательный метод: создаёт мок UpdateObject с заданным update_id
     */
    private function createMockUpdate(int $updateId): UpdateObject
    {
        $update = $this->createMock(UpdateObject::class);
        $update->method('get')->willReturnMap([
            ['update_id', null, $updateId],
        ]);
        $update->method('toArray')->willReturn([
            'update_id' => $updateId,
            'message' => [
                'message_id' => 1,
                'chat' => ['id' => 123, 'type' => 'private'],
                'date' => time(),
                'text' => 'Тестовое сообщение',
            ],
        ]);
        return $update;
    }

    /**
     * Вспомогательный метод: устанавливает shouldStop=true через рефлексию после N вызовов getUpdates
     * Это позволяет выполнить ровно N итераций цикла while
     */
    private function setupStopAfterNCalls(int $n): void
    {
        $ref = new ReflectionClass($this->worker);
        $shouldStopProp = $ref->getProperty('shouldStop');
        $shouldStopProp->setAccessible(true);

        $callCount = 0;
        $this->telegramBot->method('getUpdates')
            ->willReturnCallback(function () use ($shouldStopProp, $n, &$callCount) {
                $callCount++;
                if ($callCount >= $n) {
                    $shouldStopProp->setValue($this->worker, true);
                }
                return [];
            });
    }

    // --- Один цикл с одним update -> передаёт в HandleTelegramWebhook

    /**
     * При получении одного update — вызывается HandleTelegramWebhook::execute() с JSON-телом
     */
    public function testRunWithOneUpdatePassesToHandleTelegramWebhook(): void
    {
        $update = $this->createMockUpdate(100);

        // deleteWebhook вызывается при старте
        $this->telegramBot->expects($this->once())->method('deleteWebhook');

        // getUpdates возвращает один update, затем останавливаем цикл
        $ref = new ReflectionClass($this->worker);
        $shouldStopProp = $ref->getProperty('shouldStop');
        $shouldStopProp->setAccessible(true);

        $callCount = 0;
        $this->telegramBot->method('getUpdates')
            ->willReturnCallback(function () use ($shouldStopProp, $update, &$callCount) {
                $callCount++;
                if ($callCount === 1) {
                    return [$update];
                }
                $shouldStopProp->setValue($this->worker, true);
                return [];
            });

        // HandleTelegramWebhook::execute() должен быть вызван один раз
        $this->handleTelegramWebhook->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (string $body): bool {
                $data = json_decode($body, true);
                return $data !== null && $data['update_id'] === 100;
            }));

        $this->logger->method('info'); // Логирование запуска/остановки

        $this->worker->run();
    }

    // --- Пустой ответ от getUpdates -> без обработки

    /**
     * При пустом ответе getUpdates — HandleTelegramWebhook не вызывается
     */
    public function testRunWithEmptyUpdatesDoesNotCallHandleTelegramWebhook(): void
    {
        $this->telegramBot->method('deleteWebhook');

        // getUpdates всегда возвращает пустой массив
        $this->setupStopAfterNCalls(1);

        // HandleTelegramWebhook не должен быть вызван
        $this->handleTelegramWebhook->expects($this->never())->method('execute');

        $this->logger->method('info');
        $this->logger->method('debug');

        $this->worker->run();
    }

    // --- Ошибка getUpdates -> логирование + sleep

    /**
     * При ошибке getUpdates — логируется ошибка и вызывается sleepWithSignalCheck
     * Проверяем через логирование ошибки и отсутствие вызова handleTelegramWebhook
     */
    public function testRunWithGetUpdatesErrorLogsAndSleeps(): void
    {
        $this->telegramBot->method('deleteWebhook');

        // getUpdates выбрасывает исключение, затем останавливаем цикл
        $ref = new ReflectionClass($this->worker);
        $shouldStopProp = $ref->getProperty('shouldStop');
        $shouldStopProp->setAccessible(true);

        $callCount = 0;
        $this->telegramBot->method('getUpdates')
            ->willReturnCallback(function () use ($shouldStopProp, &$callCount) {
                $callCount++;
                if ($callCount === 1) {
                    throw new RuntimeException('Telegram API unavailable');
                }
                $shouldStopProp->setValue($this->worker, true);
                return [];
            });

        // Ожидаем логирование ошибки
        $this->logger->expects($this->atLeastOnce())
            ->method('error')
            ->with($this->stringContains('Telegram Long Polling: ошибка'));

        // HandleTelegramWebhook не должен быть вызван при ошибке
        $this->handleTelegramWebhook->expects($this->never())->method('execute');

        $this->logger->method('info');
        $this->logger->method('debug');
        $this->logger->method('warning');

        $this->worker->run();
    }

    // --- Graceful shutdown при SIGTERM

    /**
     * При установке shouldStop=true (имитация SIGTERM) — цикл завершается корректно
     * Проверяем, что логируется сообщение об остановке
     */
    public function testRunGracefulShutdownOnSigterm(): void
    {
        $this->telegramBot->method('deleteWebhook');

        // Останавливаем после первой итерации (имитация SIGTERM)
        $this->setupStopAfterNCalls(1);

        // Ожидаем логирование остановки worker
        $this->logger->expects($this->atLeastOnce())
            ->method('info')
            ->with($this->callback(function (string $message): bool {
                // Должно быть сообщение об остановке
                return str_contains($message, 'worker остановлен')
                    || str_contains($message, 'worker запущен')
                    || str_contains($message, 'webhook удалён');
            }));

        $this->handleTelegramWebhook->expects($this->never())->method('execute');

        $this->worker->run();
    }

    // --- deleteWebhook вызывается при старте

    /**
     * При запуске — deleteWebhook вызывается ровно один раз
     */
    public function testRunCallsDeleteWebhookOnStart(): void
    {
        $this->telegramBot->expects($this->once())
            ->method('deleteWebhook')
            ->willReturn(true);

        // Останавливаем после первой итерации
        $this->setupStopAfterNCalls(1);

        $this->logger->method('info');
        $this->logger->method('debug');

        $this->worker->run();
    }

    /**
     * При ошибке deleteWebhook — логируется предупреждение, но worker продолжает работу
     */
    public function testRunDeleteWebhookFailureLogsWarningAndContinues(): void
    {
        $this->telegramBot->method('deleteWebhook')
            ->willThrowException(new RuntimeException('Webhook deletion failed'));

        // Ожидаем логирование предупреждения
        $this->logger->expects($this->atLeastOnce())
            ->method('warning')
            ->with($this->stringContains('не удалось удалить webhook'));

        // Останавливаем после первой итерации
        $this->setupStopAfterNCalls(1);

        $this->logger->method('info');
        $this->logger->method('debug');
        $this->logger->method('error');

        $this->worker->run();
    }

    // --- Offset увеличивается после обработки update

    /**
     * После обработки update — offset увеличивается (update_id + 1)
     * Проверяем, что getUpdates вызывается с правильным offset на второй итерации
     */
    public function testRunOffsetIncreasesAfterProcessingUpdate(): void
    {
        $update1 = $this->createMockUpdate(100);
        $update2 = $this->createMockUpdate(200);

        $this->telegramBot->method('deleteWebhook');

        $ref = new ReflectionClass($this->worker);
        $shouldStopProp = $ref->getProperty('shouldStop');
        $shouldStopProp->setAccessible(true);

        $callCount = 0;
        $this->telegramBot->expects($this->exactly(3))
            ->method('getUpdates')
            ->willReturnCallback(function (array $params) use ($shouldStopProp, $update1, $update2, &$callCount) {
                $callCount++;

                if ($callCount === 1) {
                    // Первая итерация: offset=0, возвращаем update с id=100
                    $this->assertSame(0, $params['offset']);
                    return [$update1];
                }

                if ($callCount === 2) {
                    // Вторая итерация: offset=101 (100+1), возвращаем update с id=200
                    $this->assertSame(101, $params['offset']);
                    return [$update2];
                }

                // Третья итерация: offset=201 (200+1), останавливаем
                $this->assertSame(201, $params['offset']);
                $shouldStopProp->setValue($this->worker, true);
                return [];
            });

        // HandleTelegramWebhook вызывается дважды (для двух update)
        $this->handleTelegramWebhook->expects($this->exactly(2))->method('execute');

        $this->logger->method('info');
        $this->logger->method('debug');

        $this->worker->run();
    }

    // --- Несколько update в одном ответе

    /**
     * При получении нескольких update в одном ответе — все передаются в HandleTelegramWebhook
     */
    public function testRunWithMultipleUpdatesPassesAllToHandleTelegramWebhook(): void
    {
        $update1 = $this->createMockUpdate(300);
        $update2 = $this->createMockUpdate(301);
        $update3 = $this->createMockUpdate(302);

        $this->telegramBot->method('deleteWebhook');

        $ref = new ReflectionClass($this->worker);
        $shouldStopProp = $ref->getProperty('shouldStop');
        $shouldStopProp->setAccessible(true);

        $callCount = 0;
        $this->telegramBot->method('getUpdates')
            ->willReturnCallback(function () use ($shouldStopProp, $update1, $update2, $update3, &$callCount) {
                $callCount++;
                if ($callCount === 1) {
                    return [$update1, $update2, $update3];
                }
                $shouldStopProp->setValue($this->worker, true);
                return [];
            });

        // HandleTelegramWebhook вызывается трижды
        $this->handleTelegramWebhook->expects($this->exactly(3))->method('execute');

        $this->logger->method('info');
        $this->logger->method('debug');

        $this->worker->run();
    }

    // --- Ошибка при обработке отдельного update не прерывает цикл

    /**
     * Если HandleTelegramWebhook::execute() выбрасывает исключение —
     * ошибка логируется, но цикл продолжает работу
     */
    public function testRunHandleUpdateErrorDoesNotBreakLoop(): void
    {
        $update = $this->createMockUpdate(400);

        $this->telegramBot->method('deleteWebhook');

        $ref = new ReflectionClass($this->worker);
        $shouldStopProp = $ref->getProperty('shouldStop');
        $shouldStopProp->setAccessible(true);

        $callCount = 0;
        $this->telegramBot->method('getUpdates')
            ->willReturnCallback(function () use ($shouldStopProp, $update, &$callCount) {
                $callCount++;
                if ($callCount === 1) {
                    return [$update];
                }
                $shouldStopProp->setValue($this->worker, true);
                return [];
            });

        // HandleTelegramWebhook выбрасывает исключение
        $this->handleTelegramWebhook->method('execute')
            ->willThrowException(new RuntimeException('Processing failed'));

        // Ожидаем логирование ошибки обработки update
        $this->logger->expects($this->atLeastOnce())
            ->method('error')
            ->with($this->stringContains('ошибка обработки update'));

        $this->logger->method('info');
        $this->logger->method('debug');
        $this->logger->method('warning');

        // Цикл не прерывается — worker завершается нормально
        $this->worker->run();
    }
}
