<?php

namespace BookstoreApp\Infrastructure\Persistence\ActiveRecord;

use AllowDynamicProperties;
use PDO;
use BookstoreApp\Domain\Entity\Bookstore;
use BookstoreApp\Infrastructure\Database\Connection;

class BookstoreActiveRecord extends Bookstore
{
    private static ?PDO $connection = null;

    public function __construct(?array $data = null)
    {
        if ($data) {
            parent::__construct(
                $data['id'] ?? null,
                $data['name'],
                $data['city'],
                $data['address'],
                $data['phone'] ?? null,
                $data['email'] ?? null,
                $data['established_year'] ?? null,
                $data['square_meters'] ?? null,
                $data['has_cafe'] ?? false,
                $data['rating'] ?? null,
                $data['created_at'] ?? null,
                $data['updated_at'] ?? null
            );
        } else {
            parent::__construct(null, '', '', '');
        }
    }

    public static function initConnection(Connection $connection): void
    {
        self::$connection = $connection->getConnection();
    }

    public static function find(int $id): ?self
    {
        if (!self::$connection) {
            throw new \RuntimeException("Database connection not initialized. Call initConnection() first.");
        }

        $stmt = self::$connection->prepare("SELECT * FROM bookstores WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        return $data ? new static($data) : null;
    }

    public static function findAll(): array
    {
        if (!self::$connection) {
            throw new \RuntimeException("Database connection not initialized. Call initConnection() first.");
        }

        $stmt = self::$connection->query("SELECT * FROM bookstores ORDER BY id");
        $results = [];

        while ($data = $stmt->fetch()) {
            $results[] = new static($data);
        }

        return $results;
    }

    public static function findByCity(string $city): array
    {
        if (!self::$connection) {
            throw new \RuntimeException("Database connection not initialized. Call initConnection() first.");
        }

        $stmt = self::$connection->prepare("SELECT * FROM bookstores WHERE city = :city ORDER BY name");
        $stmt->execute(['city' => $city]);
        $results = [];

        while ($data = $stmt->fetch()) {
            $results[] = new static($data);
        }

        return $results;
    }

    public function save(): bool
    {
        if (!self::$connection) {
            throw new \RuntimeException("Database connection not initialized. Call initConnection() first.");
        }

        if ($this->getId() === null) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    private function insert(): bool
    {
        $sql = "INSERT INTO bookstores (name, city, address, phone, email, established_year, square_meters, has_cafe, rating) 
                VALUES (:name, :city, :address, :phone, :email, :established_year, :square_meters, :has_cafe, :rating) 
                RETURNING id";

        $stmt = self::$connection->prepare($sql);
        $success = $stmt->execute($this->toArrayWithoutId());

        if ($success) {
            $this->setId(self::$connection->lastInsertId());
        }

        return $success;
    }

    private function update(): bool
    {
        $sql = "UPDATE bookstores SET 
                name = :name, city = :city, address = :address, phone = :phone, 
                email = :email, established_year = :established_year, 
                square_meters = :square_meters, has_cafe = :has_cafe, rating = :rating,
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";

        $data = $this->toArrayWithoutId();
        $data['id'] = $this->getId();

        $stmt = self::$connection->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(): bool
    {
        if (!self::$connection) {
            throw new \RuntimeException("Database connection not initialized. Call initConnection() first.");
        }

        if ($this->getId() === null) {
            return false;
        }

        $stmt = self::$connection->prepare("DELETE FROM bookstores WHERE id = :id");
        return $stmt->execute(['id' => $this->getId()]);
    }

    private function toArrayWithoutId(): array
    {
        $data = $this->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at']);
        //var_dump($data);
        //die;
        return $data;
    }
}