<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Infrastructure\Queue;

use AMQPEnvelope;
use AMQPQueue;
use Closure;
use InvalidArgumentException;
use LogicException;
use MkdBot\Domain\Interface\FallbackMessageRepositoryInterface;
use MkdBot\Infrastructure\Queue\RabbitMQConsumer;
use MkdBot\Infrastructure\Queue\RabbitMQConnectionFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;

/**
 * Юнит-тесты для RabbitMQConsumer — базовая логика обработки сообщений, x-death, graceful shutdown
 *
 * Поскольку RabbitMQConsumer — абстрактный класс, создаём тестовый подкласс для проверки
 */
class RabbitMQConsumerTest extends TestCase
{
    private FallbackMessageRepositoryInterface $fallbackRepo;
    private LoggerInterface $logger;

    /**
     * Создаёт тестовый подкласс RabbitMQConsumer с переопределённым processMessage
     */
    private function createConsumer(callable $processMessageCallback): RabbitMQConsumer
    {
        $fallbackRepo = $this->fallbackRepo;
        $logger = $this->logger;
        $connectionFactory = new RabbitMQConnectionFactory('localhost', 5672, 'guest', 'guest', '/');

        return new class (
            $connectionFactory,
            $fallbackRepo,
            $logger,
            $processMessageCallback,
        ) extends RabbitMQConsumer {
            private Closure $processMessageCallback;

            public function __construct(
                RabbitMQConnectionFactory $connectionFactory,
                FallbackMessageRepositoryInterface $fallbackRepo,
                LoggerInterface $logger,
                callable $processMessageCallback,
            ) {
                $this->processMessageCallback = $processMessageCallback(...);
                parent::__construct($connectionFactory, 'test.queue', $fallbackRepo, $logger);
            }

            protected function processMessage(string $body, array $headers): void
            {
                ($this->processMessageCallback)($body, $headers);
            }
        };
    }

    protected function setUp(): void
    {
        $this->fallbackRepo = $this->createMock(FallbackMessageRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    // --- getXDeathCount — извлечение count из заголовков AMQP

    /**
     * Вспомогательный метод: вызывает приватный getXDeathCount через рефлексию
     */
    private function invokeGetXDeathCount(RabbitMQConsumer $consumer, array $headers): int
    {
        $method = new ReflectionMethod($consumer, 'getXDeathCount');
        return $method->invoke($consumer, $headers);
    }

    /**
     * Заголовки без x-death — count = 0
     */
    public function testGetXDeathCountReturnsZeroWhenNoXDeathHeader(): void
    {
        $consumer = $this->createConsumer(fn () => null);

        $result = $this->invokeGetXDeathCount($consumer, []);

        $this->assertSame(0, $result);
    }

    /**
     * x-death с count = 1 — первая попытка
     */
    public function testGetXDeathCountReturnsOneForFirstRetry(): void
    {
        $consumer = $this->createConsumer(fn () => null);

        $result = $this->invokeGetXDeathCount($consumer, [
            'x-death' => [['count' => 1, 'reason' => 'rejected']],
        ]);

        $this->assertSame(1, $result);
    }

    /**
     * x-death с count = 3 — превышен лимит попыток
     */
    public function testGetXDeathCountReturnsThreeForThirdRetry(): void
    {
        $consumer = $this->createConsumer(fn () => null);

        $result = $this->invokeGetXDeathCount($consumer, [
            'x-death' => [['count' => 3, 'reason' => 'expired']],
        ]);

        $this->assertSame(3, $result);
    }

    /**
     * x-death — пустой массив — count = 1 (значение по умолчанию)
     */
    public function testGetXDeathCountReturnsOneForEmptyXDeathArray(): void
    {
        $consumer = $this->createConsumer(fn () => null);

        $result = $this->invokeGetXDeathCount($consumer, [
            'x-death' => [],
        ]);

        $this->assertSame(1, $result);
    }

    /**
     * x-death — массив без ключа count — count = 1 (по умолчанию)
     */
    public function testGetXDeathCountReturnsOneWhenCountKeyMissing(): void
    {
        $consumer = $this->createConsumer(fn () => null);

        $result = $this->invokeGetXDeathCount($consumer, [
            'x-death' => [['reason' => 'rejected']],
        ]);

        $this->assertSame(1, $result);
    }

    /**
     * x-death — не массив (скалярное значение) — count = 1
     */
    public function testGetXDeathCountReturnsOneForNonArrayXDeath(): void
    {
        $consumer = $this->createConsumer(fn () => null);

        $result = $this->invokeGetXDeathCount($consumer, [
            'x-death' => 'not-an-array',
        ]);

        $this->assertSame(1, $result);
    }

    // --- handleMessage — классификация ошибок

    /**
     * RuntimeException при x-death.count >= 3 -> ACK + публикация в DLQ (fallback в БД при отсутствии канала)
     */
    public function testHandleMessageRuntimeExceptionWithHighXDeathPublishesToDLQAndAcks(): void
    {
        $consumer = $this->createConsumer(function (): void {
            throw new RuntimeException('Повторимая ошибка: 429 Too Many Requests');
        });

        $envelope = $this->createMock(AMQPEnvelope::class);
        $envelope->method('getBody')->willReturn('{"text":"test"}');
        $envelope->method('getDeliveryTag')->willReturn(10);
        $envelope->method('getHeaders')->willReturn([
            'x-death' => [['count' => 3]],
        ]);

        $queue = $this->createMock(AMQPQueue::class);
        $queue->expects($this->once())->method('ack')->with(10);
        $queue->expects($this->never())->method('nack');

        // Поскольку канал RabbitMQ не установлен (null), публикация в DLQ не удастся,
        // и fallback сохранит в БД
        $this->fallbackRepo->expects($this->once())->method('save');
        $this->logger->method('warning');
        $this->logger->method('error');
        $this->logger->method('info');

        $method = new ReflectionMethod($consumer, 'handleMessage');
        $method->invoke($consumer, $queue, $envelope);
    }

    /**
     * RuntimeException при x-death.count < 3 -> NACK (requeue=false)
     */
    public function testHandleMessageRuntimeExceptionWithLowXDeathNacks(): void
    {
        $consumer = $this->createConsumer(function (): void {
            throw new RuntimeException('Повторимая ошибка: 500 Internal Server Error');
        });

        $envelope = $this->createMock(AMQPEnvelope::class);
        $envelope->method('getBody')->willReturn('{"text":"test"}');
        $envelope->method('getDeliveryTag')->willReturn(11);
        $envelope->method('getHeaders')->willReturn([
            'x-death' => [['count' => 1]],
        ]);

        $queue = $this->createMock(AMQPQueue::class);
        $queue->expects($this->never())->method('ack');
        $queue->expects($this->once())->method('nack')->with(11, AMQP_NOPARAM);

        $this->fallbackRepo->expects($this->never())->method('save');
        $this->logger->method('warning');

        $method = new ReflectionMethod($consumer, 'handleMessage');
        $method->invoke($consumer, $queue, $envelope);
    }

    /**
     * RuntimeException без x-death заголовков -> NACK (x-death.count = 0)
     */
    public function testHandleMessageRuntimeExceptionWithoutXDeathNacks(): void
    {
        $consumer = $this->createConsumer(function (): void {
            throw new RuntimeException('Повторимая ошибка: 502 Bad Gateway');
        });

        $envelope = $this->createMock(AMQPEnvelope::class);
        $envelope->method('getBody')->willReturn('{"text":"test"}');
        $envelope->method('getDeliveryTag')->willReturn(12);
        $envelope->method('getHeaders')->willReturn([]);

        $queue = $this->createMock(AMQPQueue::class);
        $queue->expects($this->never())->method('ack');
        $queue->expects($this->once())->method('nack')->with(12, AMQP_NOPARAM);

        $this->fallbackRepo->expects($this->never())->method('save');
        $this->logger->method('warning');

        $method = new ReflectionMethod($consumer, 'handleMessage');
        $method->invoke($consumer, $queue, $envelope);
    }

    /**
     * Фатальная ошибка (не RuntimeException) -> ACK + публикация в DLQ (fallback в БД при отсутствии канала)
     */
    public function testHandleMessageFatalErrorAcksAndPublishesToDLQ(): void
    {
        $consumer = $this->createConsumer(function (): void {
            throw new InvalidArgumentException('Фатальная ошибка: 400 Bad Request');
        });

        $envelope = $this->createMock(AMQPEnvelope::class);
        $envelope->method('getBody')->willReturn('{"text":"test"}');
        $envelope->method('getDeliveryTag')->willReturn(13);
        $envelope->method('getHeaders')->willReturn([]);

        $queue = $this->createMock(AMQPQueue::class);
        $queue->expects($this->once())->method('ack')->with(13);
        $queue->expects($this->never())->method('nack');

        // Поскольку канал RabbitMQ не установлен (null), публикация в DLQ не удастся,
        // и fallback сохранит в БД
        $this->fallbackRepo->expects($this->once())->method('save');
        $this->logger->method('error');
        $this->logger->method('info');

        $method = new ReflectionMethod($consumer, 'handleMessage');
        $method->invoke($consumer, $queue, $envelope);
    }

    /**
     * Успешная обработка -> ACK, без fallback
     */
    public function testHandleMessageSuccessAcksWithoutFallback(): void
    {
        $consumer = $this->createConsumer(function (): void {
            // Успешная обработка — без исключений
        });

        $envelope = $this->createMock(AMQPEnvelope::class);
        $envelope->method('getBody')->willReturn('{"text":"test"}');
        $envelope->method('getDeliveryTag')->willReturn(14);
        $envelope->method('getHeaders')->willReturn([]);

        $queue = $this->createMock(AMQPQueue::class);
        $queue->expects($this->once())->method('ack')->with(14);
        $queue->expects($this->never())->method('nack');

        $this->fallbackRepo->expects($this->never())->method('save');

        $method = new ReflectionMethod($consumer, 'handleMessage');
        $method->invoke($consumer, $queue, $envelope);
    }

    // --- saveToFallback — публикация в DLQ через RabbitMQ

    /**
     * При отсутствии канала RabbitMQ — fallback сохраняет в БД
     */
    public function testSaveToFallbackFallsBackToDbWhenChannelIsNull(): void
    {
        $consumer = $this->createConsumer(function (): void {
            throw new LogicException('Фатальная ошибка для fallback-теста');
        });

        // Ожидаем сохранение в БД (fallback при отсутствии канала)
        $this->fallbackRepo->expects($this->once())->method('save');

        // Ожидаем логирование: error (канал не доступен) + error (фатальная ошибка)
        $this->logger->expects($this->atLeastOnce())->method('error');

        $envelope = $this->createMock(AMQPEnvelope::class);
        $envelope->method('getBody')->willReturn('{"text":"test"}');
        $envelope->method('getDeliveryTag')->willReturn(15);
        $envelope->method('getHeaders')->willReturn([]);

        $queue = $this->createMock(AMQPQueue::class);
        $queue->method('ack');

        $method = new ReflectionMethod($consumer, 'handleMessage');
        // Не должно выбросить исключение — ошибка сохранения перехватывается
        $method->invoke($consumer, $queue, $envelope);
    }

    /**
     * Ошибка при fallback-сохранении в БД — логируется, но не выбрасывается
     */
    public function testSaveToFallbackLogsErrorWhenDbFallbackAlsoFails(): void
    {
        $consumer = $this->createConsumer(function (): void {
            throw new LogicException('Фатальная ошибка для fallback-теста');
        });

        // И публикация в RabbitMQ (не удастся — нет канала), и БД fallback тоже падает
        $this->fallbackRepo->method('save')->willThrowException(new RuntimeException('БД недоступна'));

        // Ожидаем логирование ошибок: нет канала + ошибка БД fallback
        $this->logger->expects($this->atLeastOnce())->method('error');

        $envelope = $this->createMock(AMQPEnvelope::class);
        $envelope->method('getBody')->willReturn('{"text":"test"}');
        $envelope->method('getDeliveryTag')->willReturn(16);
        $envelope->method('getHeaders')->willReturn([]);

        $queue = $this->createMock(AMQPQueue::class);
        $queue->method('ack');

        $method = new ReflectionMethod($consumer, 'handleMessage');
        // Не должно выбросить исключение — обе ошибки перехватываются
        $method->invoke($consumer, $queue, $envelope);
    }

    // --- waitForProcessing — таймаут 10 сек

    /**
     * waitForProcessing — при processingMessage=false сразу завершается
     */
    public function testWaitForProcessingReturnsImmediatelyWhenNotProcessing(): void
    {
        $consumer = $this->createConsumer(fn () => null);

        // Устанавливаем processingMessage = false через рефлексию родительского класса
        $prop = new ReflectionProperty(RabbitMQConsumer::class, 'processingMessage');
        $prop->setValue($consumer, false);

        $method = new ReflectionMethod(RabbitMQConsumer::class, 'waitForProcessing');

        $start = microtime(true);
        $method->invoke($consumer);
        $elapsed = microtime(true) - $start;

        // Должно завершиться почти мгновенно (< 0.5 сек)
        $this->assertLessThan(0.5, $elapsed);
    }

    // --- Graceful shutdown — установка флага shouldStop

    /**
     * Установка shouldStop через рефлексию — флаг корректно устанавливается
     */
    public function testShouldStopFlagCanBeSet(): void
    {
        $consumer = $this->createConsumer(fn () => null);

        $prop = new ReflectionProperty(RabbitMQConsumer::class, 'shouldStop');

        // По умолчанию false
        $this->assertFalse($prop->getValue($consumer));

        // Устанавливаем true (имитация SIGTERM)
        $prop->setValue($consumer, true);
        $this->assertTrue($prop->getValue($consumer));
    }

    /**
     * processingMessage — флаг корректно отражает состояние обработки
     */
    public function testProcessingMessageFlagCanBeSet(): void
    {
        $consumer = $this->createConsumer(fn () => null);

        $prop = new ReflectionProperty(RabbitMQConsumer::class, 'processingMessage');

        // По умолчанию false
        $this->assertFalse($prop->getValue($consumer));

        // Устанавливаем true (имитация обработки сообщения)
        $prop->setValue($consumer, true);
        $this->assertTrue($prop->getValue($consumer));
    }

    // --- Параметры конструктора

    /**
     * Имя очереди корректно передаётся в конструктор
     */
    public function testConstructorSetsQueueName(): void
    {
        $consumer = $this->createConsumer(fn () => null);

        $prop = new ReflectionProperty(RabbitMQConsumer::class, 'queueName');
        $this->assertSame('test.queue', $prop->getValue($consumer));
    }

    /**
     * exchange по умолчанию null — ленивая инициализация
     */
    public function testExchangeIsNullByDefault(): void
    {
        $consumer = $this->createConsumer(fn () => null);

        $prop = new ReflectionProperty(RabbitMQConsumer::class, 'exchange');
        $this->assertNull($prop->getValue($consumer));
    }
}
