<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Entity\BotSubscriber;
use PHPUnit\Framework\TestCase;

/**
 * Тесты сущности BotSubscriber
 */
class BotSubscriberTest extends TestCase
{
    public function testCreateBotSubscriberWithDefaults(): void
    {
        $subscriber = new BotSubscriber(
            userId: 12345,
            userName: 'Иван',
        );

        $this->assertEquals(12345, $subscriber->getUserId());
        $this->assertEquals('Иван', $subscriber->getUserName());
        $this->assertInstanceOf(DateTimeImmutable::class, $subscriber->getSubscribedAt());
        $this->assertTrue($subscriber->isActive());
    }

    public function testCreateBotSubscriberWithAllParams(): void
    {
        $subscribedAt = new DateTimeImmutable('2025-01-10 08:00:00');
        $unsubscribedAt = new DateTimeImmutable('2025-01-15 10:00:00');
        $subscriber = new BotSubscriber(
            userId: 67890,
            userName: 'Мария',
            subscribedAt: $subscribedAt,
            isActive: false,
            unsubscribedAt: $unsubscribedAt,
        );

        $this->assertEquals(67890, $subscriber->getUserId());
        $this->assertEquals('Мария', $subscriber->getUserName());
        $this->assertEquals($subscribedAt, $subscriber->getSubscribedAt());
        $this->assertFalse($subscriber->isActive());
        $this->assertEquals($unsubscribedAt, $subscriber->getUnsubscribedAt());
    }

    public function testInactiveSubscriber(): void
    {
        $subscriber = new BotSubscriber(
            userId: 11111,
            userName: 'Пётр',
            isActive: false,
        );

        $this->assertFalse($subscriber->isActive());
    }

    public function testDefaultUnsubscribedAtIsNull(): void
    {
        $subscriber = new BotSubscriber(
            userId: 22222,
            userName: 'Анна',
        );

        $this->assertNull($subscriber->getUnsubscribedAt());
    }

    public function testMarkAsUnsubscribedSetsInactiveAndTimestamp(): void
    {
        $subscriber = new BotSubscriber(
            userId: 33333,
            userName: 'Олег',
        );

        $this->assertTrue($subscriber->isActive());
        $this->assertNull($subscriber->getUnsubscribedAt());

        $subscriber->markAsUnsubscribed();

        $this->assertFalse($subscriber->isActive());
        $this->assertInstanceOf(DateTimeImmutable::class, $subscriber->getUnsubscribedAt());
    }

    public function testMarkAsResubscribedSetsActiveAndResetsTimestamps(): void
    {
        $subscribedAt = new DateTimeImmutable('2025-01-10 08:00:00');
        $unsubscribedAt = new DateTimeImmutable('2025-01-15 10:00:00');
        $subscriber = new BotSubscriber(
            userId: 44444,
            userName: 'Елена',
            subscribedAt: $subscribedAt,
            isActive: false,
            unsubscribedAt: $unsubscribedAt,
        );

        $this->assertFalse($subscriber->isActive());
        $this->assertEquals($unsubscribedAt, $subscriber->getUnsubscribedAt());

        $subscriber->markAsResubscribed();

        $this->assertTrue($subscriber->isActive());
        $this->assertNull($subscriber->getUnsubscribedAt());
        $this->assertNotEquals($subscribedAt, $subscriber->getSubscribedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $subscriber->getSubscribedAt());
    }
}
