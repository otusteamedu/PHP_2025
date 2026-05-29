<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Persistence;

use MkdBot\Domain\Entity\Contact;
use MkdBot\Domain\Enum\ContactType;
use MkdBot\Domain\Interface\ContactRepositoryInterface;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;

/**
 * Репозиторий контактов (УК и совет дома) — реализация через Postgres
 */
class PostgresContactRepository implements ContactRepositoryInterface
{
    public function __construct(
        private readonly DatabaseConnectionInterface $db,
    ) {
    }

    public function findByType(ContactType $type): array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare(
            "SELECT id, type, name, role, phone, email, description, sort
            FROM contacts
            WHERE type = ?
            ORDER BY sort",
        );
        $stmt->execute([$type->value]);

        $contacts = [];
        while ($row = $stmt->fetch()) {
            $contacts[] = $this->hydrate($row);
        }

        return $contacts;
    }

    /**
     * Гидратация строки БД в сущность Contact
     */
    private function hydrate(array $row): Contact
    {
        return new Contact(
            id: (int)$row['id'],
            type: ContactType::from((string)$row['type']),
            name: $row['name'],
            role: $row['role'],
            phone: $row['phone'] ?? '',
            email: $row['email'] ?? '',
            description: $row['description'] ?? '',
            sort: (int)$row['sort'],
        );
    }
}
