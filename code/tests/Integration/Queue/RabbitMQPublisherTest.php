<?php

declare(strict_types=1);

namespace MkdBot\Tests\Integration\Queue;

use AMQPException;
use MkdBot\Domain\Enum\QueueNameType;
use MkdBot\Domain\Interface\QueuePublisherInterface;
use MkdBot\Infrastructure\Queue\RabbitMQConnectionFactory;
use MkdBot\Infrastructure\Queue\RabbitMQPublisher;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Интеграционный тест RabbitMQPublisher
 * Требует запущенный RabbitMQ — пропускается если недоступен
 */
class RabbitMQPublisherTest extends TestCase
{
    private ?QueuePublisherInterface $publisher = null;

    protected function setUp(): void
    {
        $host = getenv('RABBITMQ_HOST') ?: 'rabbitmq';
        $port = (int)(getenv('RABBITMQ_PORT') ?: 5672);
        $login = getenv('RABBITMQ_LOGIN') ?: 'guest';
        $password = getenv('RABBITMQ_PASSWORD') ?: 'guest';
        $vhost = getenv('RABBITMQ_VHOST') ?: '/';

        $logger = $this->createMock(LoggerInterface::class);

        try {
            $connectionFactory = new RabbitMQConnectionFactory($host, $port, $login, $password, $vhost);
            $this->publisher = new RabbitMQPublisher($connectionFactory, $logger);
        } catch (AMQPException $e) {
            $this->markTestSkipped('RabbitMQ недоступен: ' . $e->getMessage());
        }
    }

    public function testPublishMessage(): void
    {
        if ($this->publisher === null) {
            $this->markTestSkipped('Publisher не инициализирован');
        }

        try {
            // Публикуем тестовое сообщение в очередь
            $this->publisher->publish(QueueNameType::TelegramForward, [
                'text' => 'Integration test message',
                'source_message_mid' => 'mid.test_' . uniqid('', true),
                'chat_id' => 0,
                'chat_type' => 'channel',
            ]);

            // Если исключение не выброшено — тест пройден
            $this->assertTrue(true, 'Сообщение успешно опубликовано в RabbitMQ');
        } catch (AMQPException $e) {
            $this->markTestSkipped('RabbitMQ недоступен при публикации: ' . $e->getMessage());
        }
    }
}
