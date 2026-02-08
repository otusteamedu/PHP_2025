<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Infrastructure\Bus;

use Otus\Food\Infrastructure\Bus\Bus;
use PHPUnit\Framework\TestCase;

class BusTest extends TestCase
{
    public function testBus(): void
    {
        $bus = new Bus();
        $command = 'test.command';
        $handler = function (string $arg): int {
            return strlen($arg);
        };

        $bus->register($command, $handler);
        $this->assertTrue($bus->has($command));

        $result = $bus->handle($command, ['hello']);
        $this->assertSame(5, $result);

        $bus->unregister($command);
        $this->assertFalse($bus->has($command));
    }
}
