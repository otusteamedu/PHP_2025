<?php
declare(strict_types=1);

namespace App;

use PDO;
use IteratorAggregate;
use ArrayIterator;

class ProductCollection implements IteratorAggregate
{
    private PDO $pdo;
    private string $query;

    private ?array $products = null;

    public function __construct(PDO $pdo, string $query)
    {
        $this->pdo = $pdo;
        $this->query = $query;
    }

    //для Lazy Load 
    private function load(): void
    {
        if ($this->products === null) {
            $stmt = $this->pdo->query($this->query);
            $this->products = $stmt->fetchAll();
        }
    }

    
    //ArrayIterator для обхода коллекции через foreach.
    public function getIterator(): ArrayIterator
    {
        $this->load();
        return new ArrayIterator($this->products);
    }

    public function count(): int
    {
        $this->load();
        return count($this->products);
    }
}
