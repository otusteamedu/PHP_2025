<?php

namespace BookstoreApp\Infrastructure\Persistence\RawDataGateway;

use BookstoreApp\Infrastructure\Database\Connection;
use PDO;

class BookstoreRawGateway
{
    private PDO $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection->getConnection();
    }

    public function fetchAllBookstores(): array
    {
        return $this->connection->query("SELECT * FROM bookstores ORDER BY city, name")->fetchAll();
    }

    public function fetchBookstoresByRating(float $minRating): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM bookstores WHERE rating >= :rating ORDER BY rating DESC");
        $stmt->execute(['rating' => $minRating]);
        return $stmt->fetchAll();
    }

    public function fetchBookstoresWithCafe(): array
    {
        return $this->connection->query("SELECT * FROM bookstores WHERE has_cafe = TRUE ORDER BY name")->fetchAll();
    }

    public function getCitiesWithBookstores(): array
    {
        $stmt = $this->connection->query("SELECT DISTINCT city FROM bookstores ORDER BY city");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getAverageRatingByCity(): array
    {
        $stmt = $this->connection->query("
            SELECT city, AVG(rating) as avg_rating, COUNT(*) as count 
            FROM bookstores 
            GROUP BY city 
            ORDER BY avg_rating DESC
        ");
        return $stmt->fetchAll();
    }
}