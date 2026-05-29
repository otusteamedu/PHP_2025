<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\Enum;

use MkdBot\Domain\Enum\MessageType;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * Тесты Enum MessageType
 */
class MessageTypeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals('text', MessageType::Text->value);
        $this->assertEquals('photo', MessageType::Photo->value);
        $this->assertEquals('document', MessageType::Document->value);
    }

    public function testFromString(): void
    {
        $this->assertEquals(MessageType::Text, MessageType::from('text'));
        $this->assertEquals(MessageType::Photo, MessageType::from('photo'));
        $this->assertEquals(MessageType::Document, MessageType::from('document'));
    }

    public function testInvalidValue(): void
    {
        $this->expectException(ValueError::class);
        MessageType::from('video');
    }

    public function testTryFromValid(): void
    {
        $this->assertEquals(MessageType::Text, MessageType::tryFrom('text'));
        $this->assertEquals(MessageType::Photo, MessageType::tryFrom('photo'));
    }

    public function testTryFromInvalidReturnsNull(): void
    {
        $this->assertNull(MessageType::tryFrom('video'));
        $this->assertNull(MessageType::tryFrom('audio'));
    }

    public function testAllCases(): void
    {
        $cases = MessageType::cases();
        $this->assertCount(3, $cases);
        $this->assertContains(MessageType::Text, $cases);
        $this->assertContains(MessageType::Photo, $cases);
        $this->assertContains(MessageType::Document, $cases);
    }
}
