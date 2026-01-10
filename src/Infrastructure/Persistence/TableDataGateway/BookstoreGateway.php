<?php

namespace BookstoreApp\Infrastructure\Persistence\TableDataGateway;

use BookstoreApp\Infrastructure\Database\Connection;
use PDO;

class BookstoreGateway
{
    private PDO $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection->getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM bookstores ORDER BY id");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->connection->prepare("SELECT * FROM bookstores WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByCity(string $city): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM bookstores WHERE city = :city ORDER BY name");
        $stmt->execute(['city' => $city]);
        return $stmt->fetchAll();
    }

    public function insert(array $data): int
    {
        $sql = "INSERT INTO bookstores (name, city, address, phone, email, established_year, square_meters, has_cafe, rating) 
                VALUES (:name, :city, :address, :phone, :email, :established_year, :square_meters, :has_cafe, :rating) 
                RETURNING id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);

        return $stmt->fetchColumn();
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $sql = "UPDATE bookstores SET 
                name = :name, city = :city, address = :address, phone = :phone, 
                email = :email, established_year = :established_year, 
                square_meters = :square_meters, has_cafe = :has_cafe, rating = :rating,
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM bookstores WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}