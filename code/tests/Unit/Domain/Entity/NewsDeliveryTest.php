<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Entity\NewsDelivery;
use MkdBot\Domain\Enum\NewsDeliveryStatus;
use PHPUnit\Framework\TestCase;

/**
 * Тесты сущности NewsDelivery
 */
class NewsDeliveryTest extends TestCase
{
    public function testCreateNewsDeliveryWithDefaults(): void
    {
        $delivery = new NewsDelivery(
            newsId: 1,
            userId: 12345,
        );

        $this->assertEquals(1, $delivery->getNewsId());
        $this->assertEquals(12345, $delivery->getUserId());
        $this->assertEquals(NewsDeliveryStatus::Pending, $delivery->getStatus());
        $this->assertNull($delivery->getDeliveredAt());
    }

    public function testCreateNewsDeliveryWithAllParams(): void
    {
        $deliveredAt = new DateTimeImmutable('2025-01-15 12:00:00');
        $delivery = new NewsDelivery(
            newsId: 5,
            userId: 67890,
            status: NewsDeliveryStatus::Sent,
            deliveredAt: $deliveredAt,
        );

        $this->assertEquals(5, $delivery->getNewsId());
        $this->assertEquals(67890, $delivery->getUserId());
        $this->assertEquals(NewsDeliveryStatus::Sent, $delivery->getStatus());
        $this->assertEquals($deliveredAt, $delivery->getDeliveredAt());
    }

    public function testStatusChecks(): void
    {
        $pending = new NewsDelivery(status: NewsDeliveryStatus::Pending);
        $this->assertTrue($pending->isPending());
        $this->assertFalse($pending->isSent());
        $this->assertFalse($pending->isFailed());

        $sent = new NewsDelivery(status: NewsDeliveryStatus::Sent);
        $this->assertFalse($sent->isPending());
        $this->assertTrue($sent->isSent());
        $this->assertFalse($sent->isFailed());

        $failed = new NewsDelivery(status: NewsDeliveryStatus::Failed);
        $this->assertFalse($failed->isPending());
        $this->assertFalse($failed->isSent());
        $this->assertTrue($failed->isFailed());
    }

    public function testMarkAsSent(): void
    {
        $delivery = new NewsDelivery(status: NewsDeliveryStatus::Pending);
        $this->assertTrue($delivery->isPending());

        $delivery->markAsSent();
        $this->assertTrue($delivery->isSent());
        $this->assertFalse($delivery->isPending());
    }

    public function testMarkAsFailed(): void
    {
        $delivery = new NewsDelivery(status: NewsDeliveryStatus::Pending);
        $this->assertTrue($delivery->isPending());

        $delivery->markAsFailed();
        $this->assertTrue($delivery->isFailed());
        $this->assertFalse($delivery->isPending());
    }
}
