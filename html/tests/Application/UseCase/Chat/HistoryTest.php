<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Application\UseCase\Chat;

use Otus\Queue\Application\Interface\ChatRepositoryInterface;
use Otus\Queue\Application\UseCase\Chat\History;
use Otus\Queue\Domain\Entity\Message;
use PHPUnit\Framework\TestCase;

final class HistoryTest extends TestCase
{
    public function testHandleReturnsRepositoryHistory(): void
    {
        $history = [new Message('a', 'b', 1), new Message('c', 'd', 2)];

        $repository = new class ($history) implements ChatRepositoryInterface {
            public function __construct(private array $history)
            {
            }

            public function store(Message $message): bool
            {
                return true;
            }

            public function history(int $limit = 20): array
            {
                return $this->history;
            }
        };

        $useCase = new History($repository);

        self::assertSame($history, $useCase->handle());
    }
}
