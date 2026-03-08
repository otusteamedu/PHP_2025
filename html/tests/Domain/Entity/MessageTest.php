<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Domain\Entity;

use Otus\Queue\Domain\Entity\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testToArrayReturnsExpectedPayload(): void
    {
        $message = new Message('alex', 'hello', 1234567890);

        self::assertSame(
            [
                'author' => 'alex',
                'text' => 'hello',
                'created_at' => 1234567890,
            ],
            $message->toArray()
        );
    }
}
