<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Bus;

use Otus\Queue\Infrastructure\Bus\Bus;
use Otus\Queue\Infrastructure\Dic\Container;
use PHPUnit\Framework\TestCase;

final class BusTest extends TestCase
{
    protected function setUp(): void
    {
        $reflection = new \ReflectionClass(Container::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
    }

    public function testRegisterHasAndUnregisterFlow(): void
    {
        $bus = new Bus();

        $bus->register('ping', static fn (): int => 0);

        self::assertTrue($bus->has('ping'));

        $bus->unregister('ping');

        self::assertFalse($bus->has('ping'));
    }

    public function testHandleInvokesClosureHandler(): void
    {
        $bus = new Bus([
            'sum' => static fn (int $a, int $b): int => $a + $b,
        ]);

        self::assertSame(5, $bus->handle('sum', [2, 3]));
    }

    public function testHandleResolvesClassStringHandlerFromContainer(): void
    {
        Container::getInstance()->setSingleton(BusHandler::class, static fn (): BusHandler => new BusHandler());

        $bus = new Bus([
            'class-handler' => BusHandler::class,
        ]);

        self::assertSame(42, $bus->handle('class-handler'));
    }
}

final class BusHandler
{
    public function __invoke(): int
    {
        return 42;
    }
}
