<?php

declare(strict_types=1);

namespace Otus\DataMapper\Mapper;

use Otus\DataMapper\Cast\DateTime;
use Otus\DataMapper\Collection\Eager;
use Otus\DataMapper\Collection\Lazy;
use Otus\DataMapper\Entity\Product;
use Otus\DataMapper\Sql\Command;
use Otus\DataMapper\Sql\From;
use PDO;
use PDOStatement;

final class ProductMapper
{
    /**
     * @var PDOStatement
     */
    private readonly PDOStatement $insert;

    /**
     * @var PDOStatement
     */
    private readonly PDOStatement $update;

    /**
     * @var PDOStatement
     */
    private readonly PDOStatement $delete;

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
    public function __construct(private readonly PDO $pdo, private readonly string $table)
    {
        $this->insert = $this->pdo->prepare(sprintf(
            'INSERT INTO %s (%s) VALUES (%s) RETURNING id',
            $this->table,
            implode(', ', $this->fields),
            implode(', ', array_map(static function (string $field): string {
                return ':' . $field;
            }, $this->fields)),
        ));

        $this->update = $this->pdo->prepare(sprintf(
            'UPDATE %s SET %s WHERE id = :id;',
            $this->table,
            implode(', ', array_map(static function (string $field): string {
                return $field . ' = :' . $field;
            }, $this->fields)),
        ));

        $this->delete = $this->pdo->prepare(sprintf(
            'DELETE FROM %s WHERE id = :id;',
            $this->table,
        ));
    }

    /**
     * @param Command $command
     * @return PDOStatement
     */
    public function find(Command $command): PDOStatement
    {
        $command
            ->from(new From($this->table));

        $stmt = $this->pdo->prepare($command->toSql());

        $stmt->execute($command->toValues());

        return $stmt;
    }

    /**
     * @param Command $command
     * @return Lazy
     */
    public function lazy(Command $command): Lazy
    {
        $stmt = $this->find($command);

        return new Lazy($stmt->getIterator());
    }

    /**
     * @param Command $command
     * @return Eager
     */
    public function eager(Command $command): Eager
    {
        $stmt = $this->find($command);

        return new Eager($stmt->fetchAll());
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function insert(Product $product): bool
    {
        $this->insert->bindValue(':brand', $product->brand);
        $this->insert->bindValue(':title', $product->title);
        $this->insert->bindValue(':price', $product->price);
        $this->insert->bindValue(':capacity', $product->capacity);
        $this->insert->bindValue(':hidden', $product->hidden, PDO::PARAM_BOOL);
        $this->insert->bindValue(':created_at', new DateTime($product->createdAt)->getCast());
        $this->insert->bindValue(':updated_at', new DateTime($product->updatedAt)->getCast());

        $result = $this->insert->execute();

        if ($result === true) {
            [
                'id' => $id,
            ] = $this->insert->fetch();

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
        $this->update->bindValue(':id', $product->id, PDO::PARAM_INT);

        $this->update->bindValue(':brand', $product->brand);
        $this->update->bindValue(':title', $product->title);
        $this->update->bindValue(':price', $product->price);
        $this->update->bindValue(':capacity', $product->capacity);
        $this->update->bindValue(':hidden', $product->hidden, PDO::PARAM_BOOL);
        $this->update->bindValue(':created_at', new DateTime($product->createdAt)->getCast());
        $this->update->bindValue(':updated_at', new DateTime($product->updatedAt)->getCast());

        return $this->update->execute();
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function delete(Product $product): bool
    {
        $this->delete->bindValue(':id', $product->id, PDO::PARAM_INT);

        return $this->delete->execute();
    }
}
