<?php

declare(strict_types=1);

namespace Otus\Queue\Application\Persistence;

use Otus\Queue\Application\Interface\ChatRepositoryInterface;
use Otus\Queue\Domain\Entity\Message;
use Otus\Queue\Infrastructure\Database\DatabaseInterface;

final readonly class DatabaseChatRepository implements ChatRepositoryInterface
{
    /**
     * @param DatabaseInterface $database
     */
    public function __construct(
        private DatabaseInterface $database,
    ) {
    }

    /**
     * @param Message $message
     *
     * @return bool
     */
    public function store(Message $message): bool
    {
        return $this
            ->database
            ->command(
                'INSERT INTO messages (author, text, created_at) VALUES (:author, :text, :created_at);',
                [
                    ':author' => $message->author,
                    ':text' => $message->text,
                    ':created_at' => $message->createdAt,
                ]
            );
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function history(int $limit = 20): array
    {
        $iterator = $this
            ->database
            ->query(
                sprintf('SELECT author, text, created_at FROM messages ORDER BY created_at DESC LIMIT %d;', $limit)
            );

        return array_map(
            static function (array $row): Message {
                return new Message(
                    $row['author'],
                    $row['text'],
                    $row['created_at'],
                );
            },
            array_reverse(
                iterator_to_array(
                    $iterator
                )
            )
        );
    }
}
