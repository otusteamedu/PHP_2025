<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Presentation\Console;

use ArrayIterator;
use Iterator;
use Otus\Queue\Infrastructure\Database\DatabaseInterface;
use Otus\Queue\Infrastructure\Queue\Payload;
use Otus\Queue\Infrastructure\Queue\QueueInterface;
use Otus\Queue\Presentation\Console\Consumer;
use Otus\Queue\Presentation\Console\Migrate;
use Otus\Queue\Presentation\Console\Publisher;
use PHPUnit\Framework\TestCase;

final class ConsoleHandlersTest extends TestCase
{
    public function testMigrateRunsCreateTableCommand(): void
    {
        $db = new class () implements DatabaseInterface {
            public string $sql = '';

            public function command(string $sql, array $params = []): bool
            {
                $this->sql = $sql;

                return true;
            }

            public function query(string $sql): Iterator
            {
                return new ArrayIterator([]);
            }
        };

        $handler = new Migrate($db);

        self::assertSame(0, $handler());
        self::assertStringContainsString('CREATE TABLE IF NOT EXISTS messages', $db->sql);
    }

    public function testPublisherPushesMessageIntoRealtimeQueue(): void
    {
        $queue = new class () implements QueueInterface {
            public array $calls = [];

            public function push(string $connection, Payload $payload): void
            {
                $this->calls[] = ['type' => 'push', 'connection' => $connection, 'payload' => $payload];
            }

            public function pull(string $connection, Payload $payload): void
            {
                $this->calls[] = ['type' => 'pull', 'connection' => $connection, 'payload' => $payload];
            }
        };

        $handler = new Publisher($queue);

        self::assertSame(0, $handler('Hello'));
        self::assertCount(1, $queue->calls);
        self::assertSame('push', $queue->calls[0]['type']);
        self::assertSame('realtime', $queue->calls[0]['connection']);
        self::assertSame('chat', $queue->calls[0]['payload']->get('exchange'));
    }

    public function testConsumerPullsFromRealtimeQueue(): void
    {
        $queue = new class () implements QueueInterface {
            public array $calls = [];

            public function push(string $connection, Payload $payload): void
            {
                $this->calls[] = ['type' => 'push', 'connection' => $connection, 'payload' => $payload];
            }

            public function pull(string $connection, Payload $payload): void
            {
                $this->calls[] = ['type' => 'pull', 'connection' => $connection, 'payload' => $payload];
            }
        };

        $handler = new Consumer($queue);

        self::assertSame(0, $handler());
        self::assertCount(1, $queue->calls);
        self::assertSame('pull', $queue->calls[0]['type']);
        self::assertSame('realtime', $queue->calls[0]['connection']);
        self::assertSame('chat', $queue->calls[0]['payload']->get('exchange'));
    }
}
