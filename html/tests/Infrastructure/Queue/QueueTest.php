<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Queue;

use Otus\Queue\Infrastructure\Queue\Adapter\AdapterInterface;
use Otus\Queue\Infrastructure\Queue\Payload;
use Otus\Queue\Infrastructure\Queue\Queue;
use PHPUnit\Framework\TestCase;

final class QueueTest extends TestCase
{
    protected function setUp(): void
    {
        QueueAdapterFake::$created = 0;
        QueueAdapterFake::$pushed = 0;
        QueueAdapterFake::$pulled = 0;
    }

    public function testPushAndPullReuseOneAdapterInstance(): void
    {
        $queue = new Queue([
            'default' => [
                'adapter' => QueueAdapterFake::class,
                'config' => ['conn'],
            ],
        ]);

        $payload = new Payload(['a' => 1]);

        $queue->push('default', $payload);
        $queue->pull('default', $payload);

        self::assertSame(1, QueueAdapterFake::$created);
        self::assertSame(1, QueueAdapterFake::$pushed);
        self::assertSame(1, QueueAdapterFake::$pulled);
    }
}

final class QueueAdapterFake implements AdapterInterface
{
    public static int $created = 0;

    public static int $pushed = 0;

    public static int $pulled = 0;

    public function __construct(private readonly string $connection)
    {
        self::$created++;
    }

    public function push(Payload $payload): void
    {
        self::$pushed++;
    }

    public function pull(Payload $payload): void
    {
        self::$pulled++;
    }
}
