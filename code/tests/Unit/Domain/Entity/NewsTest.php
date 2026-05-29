<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Entity\News;
use MkdBot\Domain\Enum\NewsStatus;
use PHPUnit\Framework\TestCase;

/**
 * Тесты сущности News
 */
class NewsTest extends TestCase
{
    public function testCreateNewsWithDefaults(): void
    {
        $news = new News(
            title: 'Заголовок новости',
            content: 'Текст новости',
        );

        $this->assertNull($news->getId());
        $this->assertEquals('Заголовок новости', $news->getTitle());
        $this->assertEquals('Текст новости', $news->getContent());
        $this->assertEquals(0, $news->getPriority());
        $this->assertInstanceOf(DateTimeImmutable::class, $news->getCreatedAt());
        $this->assertEquals(NewsStatus::Pending, $news->getStatus());
    }

    public function testCreateNewsWithAllParams(): void
    {
        $createdAt = new DateTimeImmutable('2025-01-15 10:30:00');
        $news = new News(
            id: 42,
            title: 'Важная новость',
            content: 'Содержание важной новости',
            priority: 5,
            createdAt: $createdAt,
            status: NewsStatus::Delivering,
        );

        $this->assertEquals(42, $news->getId());
        $this->assertEquals('Важная новость', $news->getTitle());
        $this->assertEquals('Содержание важной новости', $news->getContent());
        $this->assertEquals(5, $news->getPriority());
        $this->assertEquals($createdAt, $news->getCreatedAt());
        $this->assertEquals(NewsStatus::Delivering, $news->getStatus());
    }

    public function testStatusChecks(): void
    {
        $pending = new News(status: NewsStatus::Pending);
        $this->assertTrue($pending->isPending());
        $this->assertFalse($pending->isDelivering());
        $this->assertFalse($pending->isDelivered());

        $delivering = new News(status: NewsStatus::Delivering);
        $this->assertFalse($delivering->isPending());
        $this->assertTrue($delivering->isDelivering());
        $this->assertFalse($delivering->isDelivered());

        $delivered = new News(status: NewsStatus::Delivered);
        $this->assertFalse($delivered->isPending());
        $this->assertFalse($delivered->isDelivering());
        $this->assertTrue($delivered->isDelivered());
    }

    public function testMarkAsDelivering(): void
    {
        $news = new News(status: NewsStatus::Pending);
        $this->assertTrue($news->isPending());

        $news->markAsDelivering();
        $this->assertTrue($news->isDelivering());
        $this->assertFalse($news->isPending());
    }

    public function testMarkAsDelivered(): void
    {
        $news = new News(status: NewsStatus::Delivering);
        $this->assertTrue($news->isDelivering());

        $news->markAsDelivered();
        $this->assertTrue($news->isDelivered());
        $this->assertFalse($news->isDelivering());
    }
}
