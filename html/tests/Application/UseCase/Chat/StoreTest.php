<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Application\UseCase\Chat;

use Otus\Queue\Application\Interface\ChatRepositoryInterface;
use Otus\Queue\Application\UseCase\Chat\Store;
use Otus\Queue\Domain\Entity\Message;
use Otus\Queue\Domain\Exception\ValidationException;
use Otus\Queue\Infrastructure\Queue\Payload;
use Otus\Queue\Infrastructure\Queue\QueueInterface;
use PHPUnit\Framework\TestCase;

final class StoreTest extends TestCase
{
    public function testHandleThrowsValidationExceptionForInvalidMessage(): void
    {
        $repository = new StoreRepositoryStub(true);
        $queue = new QueueSpy();

        $useCase = new Store($repository, $queue);

        $this->expectException(ValidationException::class);

        $useCase->handle(new Message(' ', '', time()));
    }

    public function testHandleStoresMessageAndPushesToQueue(): void
    {
        $repository = new StoreRepositoryStub(true);
        $queue = new QueueSpy();

        $useCase = new Store($repository, $queue);
        $message = new Message('alex', 'hello', 10);

        self::assertTrue($useCase->handle($message));

        self::assertCount(1, $repository->stored);
        self::assertCount(1, $queue->pushed);
        self::assertSame('realtime', $queue->pushed[0]['connection']);
        self::assertSame('chat', $queue->pushed[0]['payload']->get('exchange'));
        self::assertSame(json_encode($message->toArray()), $queue->pushed[0]['payload']->get('message'));
    }

    public function testHandleReturnsFalseWhenRepositoryRejectsStore(): void
    {
        $repository = new StoreRepositoryStub(false);
        $queue = new QueueSpy();

        $useCase = new Store($repository, $queue);

        self::assertFalse($useCase->handle(new Message('alex', 'hello', 10)));
        self::assertCount(1, $repository->stored);
        self::assertCount(0, $queue->pushed);
    }
}

final class StoreRepositoryStub implements ChatRepositoryInterface
{
    /** @var Message[] */
    public array $stored = [];

    public function __construct(private readonly bool $result)
    {
    }

    public function store(Message $message): bool
    {
        $this->stored[] = $message;

        return $this->result;
    }

    public function history(int $limit = 20): array
    {
        return [];
    }
}

final class QueueSpy implements QueueInterface
{
    public array $pushed = [];

    public function push(string $connection, Payload $payload): void
    {
        $this->pushed[] = [
            'connection' => $connection,
            'payload' => $payload,
        ];
    }

    public function pull(string $connection, Payload $payload): void
    {
    }
}
