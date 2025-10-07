<?php

namespace BookstoreApp\Infrastructure\Persistence\DataMapper;

use BookstoreApp\Domain\Entity\Bookstore;
use BookstoreApp\Domain\Repository\BookstoreRepositoryInterface;
use BookstoreApp\Infrastructure\Database\Connection;
use BookstoreApp\Infrastructure\Database\IdentityMap;
use PDO;

class BookstoreDataMapper implements BookstoreRepositoryInterface
{
    private PDO $connection;
    private IdentityMap $identityMap;

    public function __construct(Connection $connection, IdentityMap $identityMap)
    {
        $this->connection = $connection->getConnection();
        $this->identityMap = $identityMap;
    }

    public function findById(int $id): ?Bookstore
    {
        // Проверяем Identity Map
        if ($this->identityMap->has(Bookstore::class, $id)) {
            return $this->identityMap->get(Bookstore::class, $id);
        }

        $stmt = $this->connection->prepare("SELECT * FROM bookstores WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        $bookstore = $this->mapToEntity($data);
        $this->identityMap->set($bookstore);

        return $bookstore;
    }

    public function findAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM bookstores ORDER BY id");
        $results = [];

        while ($data = $stmt->fetch()) {
            // Проверяем Identity Map
            if ($this->identityMap->has(Bookstore::class, $data['id'])) {
                $results[] = $this->identityMap->get(Bookstore::class, $data['id']);
                continue;
            }

            $bookstore = $this->mapToEntity($data);
            $this->identityMap->set($bookstore);
            $results[] = $bookstore;
        }

        return $results;
    }

    public function findByCity(string $city): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM bookstores WHERE city = :city ORDER BY name");
        $stmt->execute(['city' => $city]);
        $results = [];

        while ($data = $stmt->fetch()) {
            if ($this->identityMap->has(Bookstore::class, $data['id'])) {
                $results[] = $this->identityMap->get(Bookstore::class, $data['id']);
                continue;
            }

            $bookstore = $this->mapToEntity($data);
            $this->identityMap->set($bookstore);
            $results[] = $bookstore;
        }

        return $results;
    }

    public function save(Bookstore $bookstore): void
    {
        if ($bookstore->getId() === null) {
            $this->insert($bookstore);
        } else {
            $this->update($bookstore);
        }

        $this->identityMap->set($bookstore);
    }

    public function delete(int $id): void
    {
        $stmt = $this->connection->prepare("DELETE FROM bookstores WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $this->identityMap->remove(Bookstore::class, $id);
    }

    private function insert(Bookstore $bookstore): void
    {
        $sql = "INSERT INTO bookstores (name, city, address, phone, email, established_year, square_meters, has_cafe, rating) 
                VALUES (:name, :city, :address, :phone, :email, :established_year, :square_meters, :has_cafe, :rating) 
                RETURNING id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($this->mapToData($bookstore));

        $bookstore->__construct($this->connection->lastInsertId(), ...array_values($this->mapToData($bookstore)));
    }

    private function update(Bookstore $bookstore): void
    {
        $sql = "UPDATE bookstores SET 
                name = :name, city = :city, address = :address, phone = :phone, 
                email = :email, established_year = :established_year, 
                square_meters = :square_meters, has_cafe = :has_cafe, rating = :rating,
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";

        $data = $this->mapToData($bookstore);
        $data['id'] = $bookstore->getId();

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);
    }

    private function mapToEntity(array $data): Bookstore
    {
        return new Bookstore(
            $data['id'],
            $data['name'],
            $data['city'],
            $data['address'],
            $data['phone'],
            $data['email'],
            $data['established_year'],
            $data['square_meters'],
            $data['has_cafe'],
            $data['rating'],
            $data['created_at'],
            $data['updated_at']
        );
    }

    private function mapToData(Bookstore $bookstore): array
    {
        return [
            'name' => $bookstore->getName(),
            'city' => $bookstore->getCity(),
            'address' => $bookstore->getAddress(),
            'phone' => $bookstore->getPhone(),
            'email' => $bookstore->getEmail(),
            'established_year' => $bookstore->getEstablishedYear(),
            'square_meters' => $bookstore->getSquareMeters(),
            'has_cafe' => $bookstore->hasCafe(),
            'rating' => $bookstore->getRating()
        ];
    }

    public function getCollection(): BookstoreCollection
    {
        return new BookstoreCollection($this->findAll());
    }
}