<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Presentation\Mapper;

use MaxMessenger\Bot\MaxBot;
use MkdBot\Application\DTO\MaxBotEventDTO;
use MkdBot\Application\DTO\MaxCallbackDTO;
use MkdBot\Application\DTO\MaxMessageDTO;
use MkdBot\Presentation\Mapper\MaxUpdateMapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Тесты для MaxUpdateMapper — маппер Update -> Domain DTO
 *
 * Используем MaxBot::makeUpdateFromString() для создания реальных Update-объектов из JSON.
 * Структура Max API:
 * - Message: { sender, recipient, body: { mid, text, attachments }, url, timestamp }
 * - User: { user_id, first_name, last_name?, username? }
 * - Callback: { callback_id, payload (JSON string), user }
 */
class MaxUpdateMapperTest extends TestCase
{
    private MaxUpdateMapper $mapper;
    private LoggerInterface $logger;

    /** Обязательный timestamp для всех Update (Unix-время в миллисекундах) */
    private const TIMESTAMP = 1704067200000;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->mapper = new MaxUpdateMapper($this->logger);
    }

    // === MessageCreatedUpdate -> MaxMessageDTO ===

    public function testMapMessageCreatedReturnsMaxMessageDTO(): void
    {
        $json = json_encode([
            'update_type' => 'message_created',
            'timestamp' => self::TIMESTAMP,
            'message' => [
                'body' => [
                    'mid' => 'mid.abc123',
                    'seq' => 1,
                    'text' => 'Привет',
                ],
                'sender' => [
                    'user_id' => 123,
                    'first_name' => 'Иван',
                ],
                'recipient' => [
                    'chat_id' => 456,
                    'chat_type' => 'dialog',
                ],
                'timestamp' => self::TIMESTAMP,
            ],
        ]);

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxMessageDTO::class, $result);
        $this->assertSame(123, $result->userId);
        $this->assertSame('Иван', $result->userName);
        $this->assertSame('Привет', $result->text);
        $this->assertSame('mid.abc123', $result->mid);
        $this->assertSame(456, $result->chatId);
        $this->assertSame('dialog', $result->chatType);
    }

    public function testMapMessageCreatedFromChannel(): void
    {
        $json = json_encode([
            'update_type' => 'message_created',
            'timestamp' => self::TIMESTAMP,
            'message' => [
                'body' => [
                    'mid' => 'mid.channel123',
                    'seq' => 2,
                    'text' => 'Объявление',
                ],
                'sender' => null,
                'recipient' => [
                    'chat_id' => 789,
                    'chat_type' => 'channel',
                ],
                'timestamp' => self::TIMESTAMP,
            ],
        ]);

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxMessageDTO::class, $result);
        $this->assertNull($result->userId);
        $this->assertNull($result->userName);
        $this->assertSame('channel', $result->chatType);
    }

    public function testMapMessageCreatedWithPhotoAttachment(): void
    {
        $json = json_encode([
            'update_type' => 'message_created',
            'timestamp' => self::TIMESTAMP,
            'message' => [
                'body' => [
                    'mid' => 'mid.photo123',
                    'seq' => 3,
                    'text' => '',
                    'attachments' => [
                        [
                            'type' => 'image',
                            'payload' => ['url' => 'https://example.com/photo.jpg'],
                        ],
                    ],
                ],
                'sender' => ['user_id' => 100, 'first_name' => 'Тест'],
                'recipient' => ['chat_id' => 200, 'chat_type' => 'dialog'],
                'timestamp' => self::TIMESTAMP,
            ],
        ]);

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxMessageDTO::class, $result);
        $this->assertCount(1, $result->attachments);
        $this->assertSame('photo', $result->attachments[0]['type']);
        $this->assertSame('https://example.com/photo.jpg', $result->attachments[0]['url']);
    }

    public function testMapMessageCreatedWithFileAttachment(): void
    {
        $json = json_encode([
            'update_type' => 'message_created',
            'timestamp' => self::TIMESTAMP,
            'message' => [
                'body' => [
                    'mid' => 'mid.file123',
                    'seq' => 4,
                    'text' => 'Документ',
                    'attachments' => [
                        [
                            'type' => 'file',
                            'filename' => 'doc.pdf',
                            'size' => 1024,
                            'payload' => [
                                'url' => 'https://example.com/doc.pdf',
                                'token' => 'file_token_123',
                            ],
                        ],
                    ],
                ],
                'sender' => ['user_id' => 100, 'first_name' => 'Тест'],
                'recipient' => ['chat_id' => 200, 'chat_type' => 'dialog'],
                'timestamp' => self::TIMESTAMP,
            ],
        ]);

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxMessageDTO::class, $result);
        $this->assertCount(1, $result->attachments);
        $this->assertSame('document', $result->attachments[0]['type']);
        $this->assertSame('https://example.com/doc.pdf', $result->attachments[0]['url']);
        $this->assertSame('doc.pdf', $result->attachments[0]['filename']);
        $this->assertSame(1024, $result->attachments[0]['size']);
    }

    public function testMapMessageCreatedWithMessageUrl(): void
    {
        $json = json_encode([
            'update_type' => 'message_created',
            'timestamp' => self::TIMESTAMP,
            'message' => [
                'body' => [
                    'mid' => 'mid.url123',
                    'seq' => 5,
                    'text' => 'Пост',
                ],
                'url' => 'https://max.chat/channel/post123',
                'sender' => null,
                'recipient' => ['chat_id' => 789, 'chat_type' => 'channel'],
                'timestamp' => self::TIMESTAMP,
            ],
        ]);

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxMessageDTO::class, $result);
        $this->assertSame('https://max.chat/channel/post123', $result->messageUrl);
    }

    public function testMapMessageCreatedWithNoAttachments(): void
    {
        $json = json_encode([
            'update_type' => 'message_created',
            'timestamp' => self::TIMESTAMP,
            'message' => [
                'body' => [
                    'mid' => 'mid.noattach',
                    'seq' => 6,
                    'text' => 'Без вложений',
                ],
                'sender' => ['user_id' => 100, 'first_name' => 'Тест'],
                'recipient' => ['chat_id' => 200, 'chat_type' => 'dialog'],
                'timestamp' => self::TIMESTAMP,
            ],
        ]);

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxMessageDTO::class, $result);
        $this->assertSame([], $result->attachments);
    }

    // === MessageCallbackUpdate -> MaxCallbackDTO ===

    public function testMapMessageCallbackReturnsMaxCallbackDTO(): void
    {
        $json = json_encode([
            'update_type' => 'message_callback',
            'timestamp' => self::TIMESTAMP,
            'callback' => [
                'callback_id' => 'cb.test123',
                'payload' => json_encode(['action' => 'contacts', 'type' => 'uk']),
                'user' => ['user_id' => 555, 'first_name' => 'Иван'],
            ],
            'message' => [
                'body' => [
                    'mid' => 'mid.msg123',
                    'seq' => 7,
                    'text' => 'Меню',
                ],
                'recipient' => ['chat_id' => 666, 'chat_type' => 'dialog'],
                'timestamp' => self::TIMESTAMP,
            ],
        ]);

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxCallbackDTO::class, $result);
        $this->assertSame('cb.test123', $result->callbackId);
        $this->assertSame('contacts', $result->payload['action']);
        $this->assertSame('uk', $result->payload['type']);
        $this->assertSame(555, $result->userId);
        $this->assertSame(666, $result->chatId);
        $this->assertSame('Иван', $result->userName);
    }

    public function testMapMessageCallbackWithInvalidJsonPayload(): void
    {
        $json = json_encode([
            'update_type' => 'message_callback',
            'timestamp' => self::TIMESTAMP,
            'callback' => [
                'callback_id' => 'cb.invalid',
                'payload' => 'not-a-json',
                'user' => ['user_id' => 555, 'first_name' => 'Тест'],
            ],
            'message' => [
                'body' => [
                    'mid' => 'mid.msg456',
                    'seq' => 8,
                    'text' => 'Меню',
                ],
                'recipient' => ['chat_id' => 666, 'chat_type' => 'dialog'],
                'timestamp' => self::TIMESTAMP,
            ],
        ]);

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxCallbackDTO::class, $result);
        // Невалидный JSON -> пустой массив (fallback)
        $this->assertSame([], $result->payload);
        $this->assertSame('Тест', $result->userName);
    }

    public function testMapMessageCallbackWithEmptyCallbackIdReturnsNull(): void
    {
        // Создаём callback с пустым callback_id — библиотека может вернуть пустую строку
        // Проверяем что маппер передаёт null и логирует warning
        $json = json_encode([
            'update_type' => 'message_callback',
            'timestamp' => self::TIMESTAMP,
            'callback' => [
                'callback_id' => '', // пустой callback_id
                'payload' => json_encode(['action' => 'contacts', 'type' => 'uk']),
                'user' => ['user_id' => 555, 'first_name' => 'Иван'],
            ],
            'message' => [
                'body' => [
                    'mid' => 'mid.msgEmpty',
                    'seq' => 10,
                    'text' => 'Меню',
                ],
                'recipient' => ['chat_id' => 666, 'chat_type' => 'dialog'],
                'timestamp' => self::TIMESTAMP,
            ],
        ]);

        // Ожидаем логирование warning о пустом callbackId
        $this->logger->expects($this->once())->method('warning')
            ->with($this->stringContains('callbackId пустой'));

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxCallbackDTO::class, $result);
        // callbackId null — answerCallback не вызовется
        $this->assertNull($result->callbackId);
        $this->assertSame('contacts', $result->payload['action']);
    }

    // === BotStartedUpdate -> MaxBotEventDTO ===

    public function testMapBotStartedReturnsMaxBotEventDTO(): void
    {
        $json = json_encode([
            'update_type' => 'bot_started',
            'timestamp' => self::TIMESTAMP,
            'chat_id' => 777,
            'user' => ['user_id' => 888, 'first_name' => 'Ольга'],
        ]);

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxBotEventDTO::class, $result);
        $this->assertSame(777, $result->chatId);
        $this->assertSame(888, $result->userId);
        $this->assertSame('Ольга', $result->userName);
        $this->assertSame('bot_started', $result->eventType);
    }

    public function testMapBotStartedWithPayload(): void
    {
        $json = json_encode([
            'update_type' => 'bot_started',
            'timestamp' => self::TIMESTAMP,
            'chat_id' => 777,
            'user' => ['user_id' => 888, 'first_name' => 'Ольга'],
            'payload' => 'deep-link-data',
        ]);

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxBotEventDTO::class, $result);
        $this->assertSame('deep-link-data', $result->payload);
    }

    // === BotStoppedUpdate -> MaxBotEventDTO ===

    public function testMapBotStoppedReturnsMaxBotEventDTO(): void
    {
        $json = json_encode([
            'update_type' => 'bot_stopped',
            'timestamp' => self::TIMESTAMP,
            'chat_id' => 999,
            'user' => ['user_id' => 111, 'first_name' => 'Пётр'],
        ]);

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertInstanceOf(MaxBotEventDTO::class, $result);
        $this->assertSame(999, $result->chatId);
        $this->assertSame(111, $result->userId);
        $this->assertSame('Пётр', $result->userName);
        $this->assertSame('bot_stopped', $result->eventType);
        $this->assertNull($result->payload);
    }

    // === Неизвестный тип update -> null ===

    public function testMapUnknownUpdateReturnsNull(): void
    {
        $json = json_encode([
            'update_type' => 'message_edited',
            'timestamp' => self::TIMESTAMP,
            'message' => [
                'body' => [
                    'mid' => 'mid.edited123',
                    'seq' => 9,
                    'text' => 'Изменённый текст',
                ],
                'sender' => ['user_id' => 100, 'first_name' => 'Тест'],
                'recipient' => ['chat_id' => 200, 'chat_type' => 'dialog'],
                'timestamp' => self::TIMESTAMP,
            ],
        ]);

        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('Неподдерживаемый тип обновления'));

        $update = MaxBot::makeUpdateFromString($json);
        $result = $this->mapper->map($update);

        $this->assertNull($result);
    }
}
