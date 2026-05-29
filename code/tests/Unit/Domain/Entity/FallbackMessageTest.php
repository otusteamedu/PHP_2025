<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Entity\FallbackMessage;
use MkdBot\Domain\Enum\QueueNameType;
use PHPUnit\Framework\TestCase;

class FallbackMessageTest extends TestCase
{
    public function testCreateFallbackMessage(): void
    {
        $message = new FallbackMessage(
            queueName: QueueNameType::TelegramForward,
            messageBody: ['text' => 'test'],
            errorMessage: 'Rate limit exceeded',
            xDeathCount: 3,
        );

        $this->assertNull($message->getId());
        $this->assertEquals(QueueNameType::TelegramForward, $message->getQueueName());
        $this->assertEquals(['text' => 'test'], $message->getMessageBody());
        $this->assertEquals('Rate limit exceeded', $message->getErrorMessage());
        $this->assertEquals(3, $message->getXDeathCount());
        $this->assertInstanceOf(DateTimeImmutable::class, $message->getCreatedAt());
    }

    public function testCreateFallbackMessageWithDefaults(): void
    {
        $message = new FallbackMessage();

        $this->assertNull($message->getId());
        $this->assertEquals(QueueNameType::TelegramForward, $message->getQueueName());
        $this->assertEquals([], $message->getMessageBody());
        $this->assertEquals('', $message->getErrorMessage());
        $this->assertEquals(0, $message->getXDeathCount());
        $this->assertInstanceOf(DateTimeImmutable::class, $message->getCreatedAt());
    }

    public function testCreateFallbackMessageWithCustomCreatedAt(): void
    {
        $createdAt = new DateTimeImmutable('2025-01-15 10:30:00');
        $message = new FallbackMessage(
            queueName: QueueNameType::RagQuery,
            messageBody: ['query' => 'test'],
            errorMessage: 'Timeout',
            xDeathCount: 1,
            createdAt: $createdAt,
        );

        $this->assertEquals($createdAt, $message->getCreatedAt());
    }

    public function testCreateFallbackMessageWithId(): void
    {
        $message = new FallbackMessage(
            id: 42,
            queueName: QueueNameType::TelegramForward,
            messageBody: ['text' => 'hello'],
            errorMessage: 'Connection refused',
            xDeathCount: 5,
        );

        $this->assertEquals(42, $message->getId());
    }
}
