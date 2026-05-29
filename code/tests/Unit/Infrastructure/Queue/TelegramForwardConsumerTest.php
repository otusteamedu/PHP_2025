<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Infrastructure\Queue;

use AMQPEnvelope;
use AMQPQueue;
use InvalidArgumentException;
use MkdBot\Application\DTO\ForwardMessageDTO;
use MkdBot\Application\UseCase\ForwardToTelegram;
use MkdBot\Domain\Interface\FallbackMessageRepositoryInterface;
use MkdBot\Infrastructure\Queue\TelegramForwardConsumer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionMethod;
use RuntimeException;

/**
 * Юнит-тесты для TelegramForwardConsumer — обработка сообщений из очереди mkd.telegram.forward
 */
class TelegramForwardConsumerTest extends TestCase
{
    private ForwardToTelegram $forwardToTelegram;
    private FallbackMessageRepositoryInterface $fallbackRepo;
    private LoggerInterface $logger;
    private TelegramForwardConsumer $consumer;

    protected function setUp(): void
    {
        $this->forwardToTelegram = $this->createMock(ForwardToTelegram::class);
        $this->fallbackRepo = $this->createMock(FallbackMessageRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        // Создаём consumer без maxApiClient (опциональная зависимость)
        $this->consumer = new TelegramForwardConsumer(
            host: 'localhost',
            port: 5672,
            login: 'guest',
            password: 'guest',
            vhost: '/',
            fallbackRepo: $this->fallbackRepo,
            logger: $this->logger,
            forwardToTelegram: $this->forwardToTelegram,
            maxApiClient: null,
        );
    }

    /**
     * Вызывает защищённый метод processMessage через рефлексию
     */
    private function invokeProcessMessage(string $body, array $headers = []): void
    {
        $method = new ReflectionMethod($this->consumer, 'processMessage');
        $method->invoke($this->consumer, $body, $headers);
    }

    // --- Обработка текстового сообщения

    /**
     * Текстовое сообщение — вызов ForwardToTelegram::execute() с правильным DTO
     */
    public function testProcessTextMessageCallsForwardToTelegram(): void
    {
        $body = json_encode([
            'text' => 'Привет из Max!',
            'source_message_mid' => 'mid.123',
            'chat_id' => 456,
            'chat_type' => 'channel',
            'message_url' => 'https://max.ru/post/123',
        ]);

        $this->forwardToTelegram->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (ForwardMessageDTO $dto): bool {
                return $dto->text === 'Привет из Max!'
                    && $dto->attachments === []
                    && $dto->sourceMessageMid === 'mid.123'
                    && $dto->chatId === 456
                    && $dto->chatType === 'channel'
                    && $dto->messageUrl === 'https://max.ru/post/123';
            }));

        $this->logger->expects($this->atLeastOnce())
            ->method('info');

        $this->invokeProcessMessage($body);
    }

    // --- Обработка сообщения с фото

    /**
     * Сообщение с фото — корректная передача вложений
     */
    public function testProcessMessageWithPhotoPassesAttachments(): void
    {
        $attachments = [
            ['type' => 'image', 'url' => 'https://cdn.max.ru/photo.jpg', 'token' => null, 'filename' => null, 'size' => 102400],
        ];

        $body = json_encode([
            'text' => 'Фото из Max',
            'attachments' => $attachments,
            'source_message_mid' => 'mid.456',
            'chat_id' => 789,
            'chat_type' => 'dialog',
        ]);

        $this->forwardToTelegram->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (ForwardMessageDTO $dto): bool {
                return count($dto->attachments) === 1
                    && $dto->attachments[0]['type'] === 'image'
                    && $dto->attachments[0]['url'] === 'https://cdn.max.ru/photo.jpg';
            }));

        $this->invokeProcessMessage($body);
    }

    // --- Обработка сообщения с документом

    /**
     * Сообщение с документом — корректная передача вложений
     */
    public function testProcessMessageWithDocumentPassesAttachments(): void
    {
        $attachments = [
            ['type' => 'document', 'url' => 'https://cdn.max.ru/file.pdf', 'token' => 'tok123', 'filename' => 'report.pdf', 'size' => 204800],
        ];

        $body = json_encode([
            'text' => 'Документ из Max',
            'attachments' => $attachments,
            'source_message_mid' => 'mid.789',
            'chat_id' => 100,
            'chat_type' => 'group',
        ]);

        $this->forwardToTelegram->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (ForwardMessageDTO $dto): bool {
                return count($dto->attachments) === 1
                    && $dto->attachments[0]['type'] === 'document'
                    && $dto->attachments[0]['url'] === 'https://cdn.max.ru/file.pdf'
                    && $dto->attachments[0]['filename'] === 'report.pdf';
            }));

        $this->invokeProcessMessage($body);
    }

    // --- Обработка сообщения с несколькими вложениями

    /**
     * Сообщение с несколькими вложениями — все передаются в DTO
     */
    public function testProcessMessageWithMultipleAttachments(): void
    {
        $attachments = [
            ['type' => 'image', 'url' => 'https://cdn.max.ru/img1.jpg', 'token' => null, 'filename' => null, 'size' => 50000],
            ['type' => 'image', 'url' => 'https://cdn.max.ru/img2.jpg', 'token' => null, 'filename' => null, 'size' => 60000],
            ['type' => 'document', 'url' => 'https://cdn.max.ru/doc.pdf', 'token' => 'tok', 'filename' => 'doc.pdf', 'size' => 100000],
        ];

        $body = json_encode([
            'text' => 'Мульти-вложение',
            'attachments' => $attachments,
            'source_message_mid' => 'mid.multi',
            'chat_id' => 200,
            'chat_type' => 'channel',
        ]);

        $this->forwardToTelegram->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (ForwardMessageDTO $dto): bool {
                return count($dto->attachments) === 3;
            }));

        $this->invokeProcessMessage($body);
    }

    // --- Обработка пустого/некорректного JSON

    /**
     * Некорректный JSON — выбрасывается RuntimeException
     */
    public function testProcessMessageWithInvalidJsonThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Не удалось декодировать JSON сообщения');

        $this->forwardToTelegram->expects($this->never())->method('execute');

        $this->invokeProcessMessage('not a json at all');
    }

    /**
     * Пустая строка вместо JSON — выбрасывается RuntimeException
     */
    public function testProcessMessageWithEmptyBodyThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);

        $this->forwardToTelegram->expects($this->never())->method('execute');

        $this->invokeProcessMessage('');
    }

    // --- Значения по умолчанию при отсутствии полей

    /**
     * Отсутствующие поля в JSON — используются значения по умолчанию
     */
    public function testProcessMessageWithMissingFieldsUsesDefaults(): void
    {
        $body = json_encode(['text' => 'Только текст']);

        $this->forwardToTelegram->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (ForwardMessageDTO $dto): bool {
                return $dto->text === 'Только текст'
                    && $dto->attachments === []
                    && $dto->sourceMessageMid === ''
                    && $dto->chatId === 0
                    && $dto->chatType === ''
                    && $dto->messageUrl === null;
            }));

        $this->invokeProcessMessage($body);
    }

    // --- Извлечение x-death заголовков

    /**
     * Тест getXDeathCount через handleMessage: при x-death.count >= 3 сообщение публикуется в fallback
     * Используем рефлексию для вызова приватного метода handleMessage с мок-объектами AMQP
     */
    public function testXDeathCountAtLeastThreeSavesToFallback(): void
    {
        // Подготавливаем consumer, у которого processMessage выбрасывает RuntimeException (повторимая ошибка)
        $consumer = new class (
            'localhost',
            5672,
            'guest',
            'guest',
            '/',
            $this->fallbackRepo,
            $this->logger,
            $this->forwardToTelegram,
        ) extends TelegramForwardConsumer {
            protected function processMessage(string $body, array $headers): void
            {
                throw new RuntimeException('Повторимая ошибка 429');
            }
        };

        // Мокаем AMQPEnvelope
        $envelope = $this->createMock(AMQPEnvelope::class);
        $envelope->method('getBody')->willReturn(json_encode(['text' => 'test']));
        $envelope->method('getDeliveryTag')->willReturn(1);
        $envelope->method('getHeaders')->willReturn([
            'x-death' => [['count' => 3, 'reason' => 'rejected', 'queue' => 'mkd.telegram.forward']],
        ]);

        // Мокаем AMQPQueue — при x-death >= 3 ожидаем ACK (не NACK)
        $queue = $this->createMock(AMQPQueue::class);
        $queue->expects($this->once())->method('ack')->with(1);
        $queue->expects($this->never())->method('nack');

        // Ожидаем сохранение в fallback
        $this->fallbackRepo->expects($this->once())->method('save');

        // Ожидаем логирование warning о превышении лимита попыток
        $this->logger->expects($this->atLeastOnce())->method('warning');

        // Вызываем handleMessage через рефлексию
        $method = new ReflectionMethod($consumer, 'handleMessage');
        $method->invoke($consumer, $queue, $envelope);
    }

    /**
     * При x-death.count < 3 и RuntimeException — сообщение получает NACK (requeue)
     */
    public function testXDeathCountLessThanThreeNacksMessage(): void
    {
        $consumer = new class (
            'localhost',
            5672,
            'guest',
            'guest',
            '/',
            $this->fallbackRepo,
            $this->logger,
            $this->forwardToTelegram,
        ) extends TelegramForwardConsumer {
            protected function processMessage(string $body, array $headers): void
            {
                throw new RuntimeException('Повторимая ошибка 429');
            }
        };

        $envelope = $this->createMock(AMQPEnvelope::class);
        $envelope->method('getBody')->willReturn(json_encode(['text' => 'test']));
        $envelope->method('getDeliveryTag')->willReturn(2);
        $envelope->method('getHeaders')->willReturn([
            'x-death' => [['count' => 1, 'reason' => 'rejected']],
        ]);

        // Ожидаем NACK (не ACK)
        $queue = $this->createMock(AMQPQueue::class);
        $queue->expects($this->never())->method('ack');
        $queue->expects($this->once())->method('nack')->with(2, AMQP_NOPARAM);

        // Не ожидаем сохранение в fallback
        $this->fallbackRepo->expects($this->never())->method('save');

        $method = new ReflectionMethod($consumer, 'handleMessage');
        $method->invoke($consumer, $queue, $envelope);
    }

    // --- Классификация ошибок

    /**
     * Фатальная ошибка (не RuntimeException) — ACK + сохранение в fallback
     */
    public function testFatalErrorCausesAckAndFallback(): void
    {
        $consumer = new class (
            'localhost',
            5672,
            'guest',
            'guest',
            '/',
            $this->fallbackRepo,
            $this->logger,
            $this->forwardToTelegram,
        ) extends TelegramForwardConsumer {
            protected function processMessage(string $body, array $headers): void
            {
                throw new InvalidArgumentException('Фатальная ошибка: неверный формат');
            }
        };

        $envelope = $this->createMock(AMQPEnvelope::class);
        $envelope->method('getBody')->willReturn(json_encode(['text' => 'test']));
        $envelope->method('getDeliveryTag')->willReturn(3);
        $envelope->method('getHeaders')->willReturn([]);

        // Фатальная ошибка -> ACK (не повторяем)
        $queue = $this->createMock(AMQPQueue::class);
        $queue->expects($this->once())->method('ack')->with(3);
        $queue->expects($this->never())->method('nack');

        // Сохранение в fallback
        $this->fallbackRepo->expects($this->once())->method('save');

        // Логирование ошибки
        $this->logger->expects($this->atLeastOnce())->method('error');

        $method = new ReflectionMethod($consumer, 'handleMessage');
        $method->invoke($consumer, $queue, $envelope);
    }

    /**
     * Успешная обработка — ACK без fallback
     */
    public function testSuccessfulProcessingCausesAck(): void
    {
        $this->forwardToTelegram->expects($this->once())->method('execute');

        $envelope = $this->createMock(AMQPEnvelope::class);
        $envelope->method('getBody')->willReturn(json_encode(['text' => 'ok']));
        $envelope->method('getDeliveryTag')->willReturn(4);
        $envelope->method('getHeaders')->willReturn([]);

        $queue = $this->createMock(AMQPQueue::class);
        $queue->expects($this->once())->method('ack')->with(4);
        $queue->expects($this->never())->method('nack');

        $this->fallbackRepo->expects($this->never())->method('save');

        $method = new ReflectionMethod($this->consumer, 'handleMessage');
        $method->invoke($this->consumer, $queue, $envelope);
    }

    // --- Документ без URL (fallback через maxApiClient)

    /**
     * Документ без URL и без maxApiClient — передаётся как есть (URL остаётся пустым)
     */
    public function testDocumentWithoutUrlAndNoMaxApiClientPassesAsIs(): void
    {
        $attachments = [
            ['type' => 'document', 'url' => '', 'token' => null, 'filename' => 'file.pdf', 'size' => null],
        ];

        $body = json_encode([
            'text' => 'Док без URL',
            'attachments' => $attachments,
            'source_message_mid' => 'mid.nourl',
            'chat_id' => 300,
            'chat_type' => 'dialog',
        ]);

        $this->forwardToTelegram->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (ForwardMessageDTO $dto): bool {
                // URL остаётся пустым — consumer без maxApiClient не может его получить
                return $dto->attachments[0]['url'] === '';
            }));

        $this->invokeProcessMessage($body);
    }
}
