<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Request\RequestRepository;
use PDO;
use RuntimeException;

final readonly class PostgresRequestRepository implements RequestRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(string $payload, string $status): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO requests (payload, status) VALUES (:payload, :status) RETURNING id',
        );
        $statement->execute([
            'payload' => $payload,
            'status' => $status,
        ]);

        $id = $statement->fetchColumn();

        if ($id === false) {
            throw new RuntimeException('Failed to persist request');
        }

        return (int) $id;
    }

    public function updateStatus(int $id, string $status): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE requests SET status = :status WHERE id = :id',
        );
        $statement->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }
}
