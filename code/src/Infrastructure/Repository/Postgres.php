<?php

declare(strict_types=1);

namespace Api\Infrastructure\Repository;

use Api\Domain\Entities\Request;
use Api\Domain\Enums\RequestStatus;
use Api\Domain\Interfaces\RepositoryInterface;

final class Postgres implements RepositoryInterface
{
    private const FORMAT = 'Y-m-d H:i:s';

    public function __construct(
        private \PDO $connection
    ) {
    }

    public function add(Request $request): Request
    {
        $sql = <<<'SQL'
            INSERT INTO requests (status, content, created)
            VALUES (:status, :content, :created)
            RETURNING id
        SQL;

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':status' => $request->status->value,
            ':content' => $request->content,
            ':created' => $request->created->format(self::FORMAT),
        ]);

        $id = (int)$stmt->fetchColumn();

        return $this->fromArray([
            'id' => $id,
            'status' => $request->status->value,
            'content' => $request->content,
            'created' => $request->created->format(self::FORMAT),
            'processed' => null,
            'result' => null,
        ]);
    }

    public function getById(int $id): ?Request
    {
        $sql = <<<'SQL'
            SELECT id, status, content, created, processed, result
            FROM requests
            WHERE id = :id
        SQL;

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->fromArray($data);
    }

    public function updateStatus(int $id, Request $request): void
    {
        $sql = <<<'SQL'
            UPDATE requests
            SET status = :status,
                result = :result,
                processed = :processed
            WHERE id = :id
        SQL;

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':status' => $request->status->value,
            ':result' => $request->result,
            ':processed' => $request->processed?->format(self::FORMAT),
        ]);
    }

    private function fromArray(array $data): Request
    {
        return new Request(
            (int)$data['id'],
            RequestStatus::from($data['status']),
            $data['content'],
            new \DateTimeImmutable($data['created']),
            $data['processed'] ? new \DateTimeImmutable($data['processed']) : null,
            $data['result']
        );
    }
}
