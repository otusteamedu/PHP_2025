<?php
namespace App\Repositories;

use App\Services\Database;

class OrderLogRepository
{
    public function __construct(private Database $db)
    {
    }

    public function log(int $orderId, string $email, bool $success, string $message = ''): void
    {
        $this->db->pdo()->prepare('INSERT INTO bank_statement_order_logs(order_id, email, success, message, created_at) VALUES(:order_id, :email, :success, :message, NOW())')
            ->execute([
                ':order_id' => $orderId,
                ':email' => $email,
                ':success' => $success,
                ':message' => $message,
            ]);
    }
}
