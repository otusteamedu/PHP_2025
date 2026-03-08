<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Kernel\Console;

use Otus\Queue\Infrastructure\Bus\Bus;
use Otus\Queue\Infrastructure\Dic\Container;
use Otus\Queue\Infrastructure\Kernel\Console\Kernel;
use Otus\Queue\Infrastructure\Kernel\Console\UnresolveHandlerException;
use PHPUnit\Framework\TestCase;

final class KernelTest extends TestCase
{
    protected function setUp(): void
    {
        $reflection = new \ReflectionClass(Container::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
    }

    public function testRunCallsRegisteredCommandWithCastedArgs(): void
    {
        $bus = new Bus();
        $bus->register('sum', static fn (int $a, int $b): int => $a + $b);

        Container::getInstance()->setSingleton(Bus::class, static fn () => $bus);

        $kernel = new Kernel([]);
        $method = new \ReflectionMethod($kernel, 'run');
        $method->setAccessible(true);

        $exitCode = $method->invoke($kernel, ['console.php', 'sum', '2', '3']);

        self::assertSame(5, $exitCode);
    }

    public function testRunThrowsWhenCommandUnknown(): void
    {
        Container::getInstance()->setSingleton(Bus::class, static fn () => new Bus());

        $kernel = new Kernel([]);
        $method = new \ReflectionMethod($kernel, 'run');
        $method->setAccessible(true);

        $this->expectException(UnresolveHandlerException::class);

        $method->invoke($kernel, ['console.php', 'missing']);
    }

    public function testCastConvertsNumericArgsToIntegers(): void
    {
        $kernel = new Kernel([]);
        $method = new \ReflectionMethod($kernel, 'cast');
        $method->setAccessible(true);

        $args = $method->invoke($kernel, ['7', 'abc', '11']);

        self::assertSame([7, 'abc', 11], $args);
    }
}
