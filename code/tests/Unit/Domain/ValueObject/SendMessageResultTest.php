<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\ValueObject;

use MkdBot\Domain\ValueObject\SendMessageResult;
use PHPUnit\Framework\TestCase;

/**
 * Тесты Domain Value Object SendMessageResult
 */
class SendMessageResultTest extends TestCase
{
    public function testCreateWithDefaults(): void
    {
        $result = new SendMessageResult();
        $this->assertSame('', $result->getMessageId());
        $this->assertSame(0, $result->getTimestamp());
    }

    public function testCreateWithParams(): void
    {
        $result = new SendMessageResult('mid.abc123', 1700000000);
        $this->assertSame('mid.abc123', $result->getMessageId());
        $this->assertSame(1700000000, $result->getTimestamp());
    }

    public function testEmptyFactory(): void
    {
        $result = SendMessageResult::empty();
        $this->assertSame('', $result->getMessageId());
        $this->assertSame(0, $result->getTimestamp());
    }

    public function testIsReadonly(): void
    {
        $result = new SendMessageResult('mid.test', 1234567890);
        $this->assertSame('mid.test', $result->getMessageId());
        $this->assertSame(1234567890, $result->getTimestamp());
    }
}
