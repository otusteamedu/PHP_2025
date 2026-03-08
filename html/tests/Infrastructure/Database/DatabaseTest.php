<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Database;

use Otus\Queue\Infrastructure\Database\Database;
use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase
{
    public function testCommandAndQueryWorkWithSqliteMemory(): void
    {
        $db = new Database('sqlite::memory:');

        self::assertTrue($db->command('CREATE TABLE messages (author TEXT, text TEXT, created_at INTEGER)', []));
        self::assertTrue(
            $db->command(
                'INSERT INTO messages (author, text, created_at) VALUES (:author, :text, :created_at)',
                [':author' => 'alex', ':text' => 'hello', ':created_at' => 1]
            )
        );

        $rows = iterator_to_array($db->query('SELECT author, text, created_at FROM messages'));

        self::assertCount(1, $rows);
        self::assertSame('alex', $rows[0]['author']);
        self::assertSame('hello', $rows[0]['text']);
        self::assertSame(1, $rows[0]['created_at']);
    }
}
