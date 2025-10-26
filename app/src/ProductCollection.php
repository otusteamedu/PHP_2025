<?php
declare(strict_types=1);

namespace App;

use PDO;
use Traversable;

class ProductCollection implements \IteratorAggregate
{
    private PDO $pdo;
    private string $query;

    public function __construct(PDO $pdo, string $query)
    {
        $this->pdo = $pdo;
        $this->query = $query;
    }

    public function getIterator(): Traversable
    {
        $stmt = $this->pdo->query($this->query);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public function count(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM products");
        return (int)$stmt->fetchColumn();
    }
}
