<?php

declare(strict_types=1);

namespace Otus\DataMapper\Infrastructure\Persistence\Mapper;

use DateMalformedStringException;
use DateTime;
use Otus\DataMapper\Domain\Entity\Product;
use PDO;
use PDOStatement;

final class ProductMapper
{
    /**
     * @var PDOStatement
     */
    private readonly PDOStatement $insertStmt;

    /**
     * @var PDOStatement
     */
    private readonly PDOStatement $updateStmt;

    /**
     * @var PDOStatement
     */
    private readonly PDOStatement $deleteStmt;

    /**
     * @var PDOStatement
     */
    private readonly PDOStatement $findByIdStmt;

    /**
     * @var array|string[]
     */
    private array $fields = [
        'brand',
        'title',
        'price',
        'capacity',
        'hidden',
        'created_at',
        'updated_at',
    ];

    /**
     * @param PDO $pdo
     * @param string $table
     */
    public function __construct(
        private readonly PDO $pdo,
        private readonly string $table = 'products'
    ) {
        $this->insertStmt = $this->pdo->prepare(sprintf(
            'INSERT INTO %s (%s) VALUES (%s) RETURNING id',
            $this->table,
            implode(', ', $this->fields),
            implode(', ', array_map(static function (string $field): string {
                return ':' . $field;
            }, $this->fields)),
        ));

        $this->updateStmt = $this->pdo->prepare(sprintf(
            'UPDATE %s SET %s WHERE id = :id;',
            $this->table,
            implode(', ', array_map(static function (string $field): string {
                return $field . ' = :' . $field;
            }, $this->fields)),
        ));

        $this->deleteStmt = $this->pdo->prepare(sprintf(
            'DELETE FROM %s WHERE id = :id;',
            $this->table,
        ));

        $this->findByIdStmt = $this->pdo->prepare(sprintf(
            'SELECT * FROM %s WHERE id = :id LIMIT 1;',
            $this->table,
        ));
    }

    /**
     * @param int $id
     *
     * @return Product|null
     *
     * @throws DateMalformedStringException
     */
    public function findById(int $id): ?Product
    {
        $this->findByIdStmt->bindValue(':id', $id, PDO::PARAM_INT);
        $this->findByIdStmt->execute();

        $row = $this->findByIdStmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * @param array $criteria
     * @param int $limit
     * @param int $offset
     *
     * @return iterable<Product>
     *
     * @throws DateMalformedStringException
     */
    public function findAll(array $criteria = [], int $limit = 100, int $offset = 0): iterable
    {
        $sql = sprintf('SELECT * FROM %s', $this->table);
        $params = [];

        if (!empty($criteria)) {
            $conditions = [];
            foreach ($criteria as $field => $value) {
                $conditions[] = sprintf('%s = :%s', $field, $field);
                $params[$field] = $value;
            }

            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= sprintf(' LIMIT %d OFFSET %d', $limit, $offset);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        foreach ($stmt as $row) {
            yield $this->hydrate($row);
        }
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function insert(Product $product): bool
    {
        $this->insertStmt->bindValue(':brand', $product->brand);
        $this->insertStmt->bindValue(':title', $product->title);
        $this->insertStmt->bindValue(':price', $product->price);
        $this->insertStmt->bindValue(':capacity', $product->capacity);
        $this->insertStmt->bindValue(':hidden', $product->hidden, PDO::PARAM_BOOL);
        $this->insertStmt->bindValue(':created_at', $product->createdAt->format('Y-m-d H:i:s'));
        $this->insertStmt->bindValue(':updated_at', $product->updatedAt->format('Y-m-d H:i:s'));

        $result = $this->insertStmt->execute();

        if ($result === true) {
            [
                'id' => $id,
            ] = $this->insertStmt->fetch();

            $product->id = $id;
        }

        return $result;
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function update(Product $product): bool
    {
        $this->updateStmt->bindValue(':id', $product->id, PDO::PARAM_INT);
        $this->updateStmt->bindValue(':brand', $product->brand);
        $this->updateStmt->bindValue(':title', $product->title);
        $this->updateStmt->bindValue(':price', $product->price);
        $this->updateStmt->bindValue(':capacity', $product->capacity);
        $this->updateStmt->bindValue(':hidden', $product->hidden, PDO::PARAM_BOOL);
        $this->updateStmt->bindValue(':created_at', $product->createdAt->format('Y-m-d H:i:s'));
        $this->updateStmt->bindValue(':updated_at', $product->updatedAt->format('Y-m-d H:i:s'));

        return $this->updateStmt->execute();
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function delete(Product $product): bool
    {
        $this->deleteStmt->bindValue(':id', $product->id, PDO::PARAM_INT);

        return $this->deleteStmt->execute();
    }

    /**
     * @param array $row
     *
     * @return Product
     *
     * @throws DateMalformedStringException
     */
    private function hydrate(array $row): Product
    {
        $product = new Product(
            brand: $row['brand'],
            title: $row['title'],
            price: (int) $row['price'],
            capacity: (int) $row['capacity'],
            hidden: (bool) $row['hidden'],
            createdAt: new DateTime($row['created_at']),
            updatedAt: new DateTime($row['updated_at']),
        );

        $product->id = (int) $row['id'];

        return $product;
    }
}
