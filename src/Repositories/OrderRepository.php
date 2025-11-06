<?php
namespace App\Repositories;

use App\Services\Database;
use PDO;

class OrderRepository
{
    public function __construct(private Database $db)
    {
    }

    public function create(string $email, string $dateFrom, string $dateTo): int
    {
        $pdo = $this->db->pdo();
        $stmt = $pdo->prepare('INSERT INTO bank_statement_orders(email, date_from, date_to, status, created_at) VALUES(:email, :date_from, :date_to, :status, NOW()) RETURNING id');
        $stmt->execute([
            ':email' => $email,
            ':date_from' => $dateFrom,
            ':date_to' => $dateTo,
            ':status' => 'queued',
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['id'];
    }

    public function markGenerated(int $id): void
    {
        $this->db->pdo()->prepare('UPDATE bank_statement_orders SET status = :status, updated_at = NOW() WHERE id = :id')->execute([
            ':status' => 'generated',
            ':id' => $id,
        ]);
    }
}
