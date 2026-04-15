<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Application\Persistence;

use ArrayIterator;
use Iterator;
use Otus\Queue\Application\Persistence\DatabaseChatRepository;
use Otus\Queue\Domain\Entity\Message;
use Otus\Queue\Infrastructure\Database\DatabaseInterface;
use PHPUnit\Framework\TestCase;

final class DatabaseChatRepositoryTest extends TestCase
{
    public function testStoreExecutesInsertWithExpectedParams(): void
    {
        $database = new DatabaseStub();
        $database->commandResult = true;

        $repository = new DatabaseChatRepository($database);
        $message = new Message('alex', 'hello', 100);

        self::assertTrue($repository->store($message));
        self::assertStringContainsString('INSERT INTO messages', $database->lastCommandSql);
        self::assertSame(
            [
                ':author' => 'alex',
                ':text' => 'hello',
                ':created_at' => 100,
            ],
            $database->lastCommandParams
        );
    }

    public function testHistoryReturnsMessagesInChronologicalOrder(): void
    {
        $database = new DatabaseStub();
        $database->queryRows = [
            ['author' => 'u2', 'text' => 'new', 'created_at' => 20],
            ['author' => 'u1', 'text' => 'old', 'created_at' => 10],
        ];

        $repository = new DatabaseChatRepository($database);

        $messages = $repository->history(2);

        self::assertCount(2, $messages);
        self::assertContainsOnlyInstancesOf(Message::class, $messages);
        self::assertSame('u1', $messages[0]->author);
        self::assertSame('u2', $messages[1]->author);
        self::assertStringContainsString('LIMIT 2', $database->lastQuerySql);
    }
}

final class DatabaseStub implements DatabaseInterface
{
    public bool $commandResult = false;

    public string $lastCommandSql = '';

    public array $lastCommandParams = [];

    public string $lastQuerySql = '';

    public array $queryRows = [];

    public function command(string $sql, array $params): bool
    {
        $this->lastCommandSql = $sql;
        $this->lastCommandParams = $params;

        return $this->commandResult;
    }

    public function query(string $sql): Iterator
    {
        $this->lastQuerySql = $sql;

        return new ArrayIterator($this->queryRows);
    }
}
