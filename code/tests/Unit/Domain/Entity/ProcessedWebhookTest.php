<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Entity\ProcessedWebhook;
use MkdBot\Domain\Enum\MessengerType;
use PHPUnit\Framework\TestCase;

class ProcessedWebhookTest extends TestCase
{
    public function testCreateProcessedWebhook(): void
    {
        $webhook = new ProcessedWebhook(
            messageMid: 'mid.abc123',
            messengerType: MessengerType::Max,
        );

        $this->assertNull($webhook->getId());
        $this->assertEquals('mid.abc123', $webhook->getMessageMid());
        $this->assertEquals(MessengerType::Max, $webhook->getMessengerType());
        $this->assertInstanceOf(DateTimeImmutable::class, $webhook->getCreatedAt());
    }

    public function testCreateProcessedWebhookWithDefaults(): void
    {
        $webhook = new ProcessedWebhook();

        $this->assertNull($webhook->getId());
        $this->assertEquals('', $webhook->getMessageMid());
        $this->assertEquals(MessengerType::Max, $webhook->getMessengerType());
        $this->assertInstanceOf(DateTimeImmutable::class, $webhook->getCreatedAt());
    }

    public function testCreateProcessedWebhookWithCustomCreatedAt(): void
    {
        $createdAt = new DateTimeImmutable('2025-06-01 12:00:00');
        $webhook = new ProcessedWebhook(
            messageMid: 'mid.xyz789',
            messengerType: MessengerType::Telegram,
            createdAt: $createdAt,
        );

        $this->assertEquals($createdAt, $webhook->getCreatedAt());
        $this->assertEquals(MessengerType::Telegram, $webhook->getMessengerType());
    }

    public function testCreateProcessedWebhookWithId(): void
    {
        $webhook = new ProcessedWebhook(
            id: 10,
            messageMid: 'mid.def456',
            messengerType: MessengerType::Max,
        );

        $this->assertEquals(10, $webhook->getId());
    }
}
