<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Persistence;

use MkdBot\Domain\Entity\Proposal;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\ProposalRepositoryInterface;

/**
 * Репозиторий предложений — реализация через Postgres
 */
class PostgresProposalRepository implements ProposalRepositoryInterface
{
    public function __construct(
        private readonly DatabaseConnectionInterface $db,
    ) {
    }

    public function save(Proposal $proposal): Proposal
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO proposals (type, user_id, user_name, subject, content, created_at)
            VALUES (?, ?, ?, ?, ?, ?)
            RETURNING id",
        );
        $stmt->execute([
            $proposal->getType()->value,
            $proposal->getUserId(),
            $proposal->getUserName(),
            $proposal->getSubject(),
            $proposal->getContent(),
            $proposal->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

        $id = (int)$stmt->fetchColumn();

        return new Proposal(
            id: $id,
            type: $proposal->getType(),
            userId: $proposal->getUserId(),
            userName: $proposal->getUserName(),
            subject: $proposal->getSubject(),
            content: $proposal->getContent(),
            createdAt: $proposal->getCreatedAt(),
        );
    }
}
