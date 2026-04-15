<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Queue;

use Otus\Queue\Infrastructure\Queue\Payload;
use PHPUnit\Framework\TestCase;

final class PayloadTest extends TestCase
{
    public function testSetOverridesValueAndReturnsSameInstance(): void
    {
        $payload = new Payload(['k' => 'v']);

        $returned = $payload->set('k', 'new');

        self::assertSame($payload, $returned);
        self::assertSame('new', $payload->get('k'));
    }
}
