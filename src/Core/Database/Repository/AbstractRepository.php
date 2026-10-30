<?php

declare(strict_types=1);

namespace App\Core\Database\Repository;

use App\Core\Database\Connection\DatabaseQueryExecutor;
use App\Core\Database\DataMapper\DataMapperInterface;
use App\Core\Database\DataMapper\IdentityMapperInterface;
use App\Core\Database\Exception\ConfigurationException;
use App\Core\Database\Exception\DatabaseException;

abstract class AbstractRepository
{
    abstract protected function getTableName(): string;
    abstract protected function getSequenceName(): string;

    public function __construct(
        protected readonly DatabaseQueryExecutor $dbQueryExecutor,
        protected readonly DataMapperInterface&IdentityMapperInterface $dataMapper,
    ) {
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    public function find(int $id): ?object
    {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE id=:id';
        $row = $this->dbQueryExecutor->queryRow($sql, [':id' => $id]);

        return !empty($row) ? $this->createEntityFromRow($row) : null;
    }

    /**
     * @return object[]
     *
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    public function findAll(): array
    {
        $sql = 'SELECT * FROM ' . $this->getTableName();
        $rows = $this->dbQueryExecutor->queryRows($sql);

        return array_map(fn(array $row) => $this->createEntityFromRow($row), $rows);
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    public function save(object $entity): void
    {
        $entity->getId() === null ? $this->insert($entity) : $this->update($entity);
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    public function delete(object $entity): void
    {
        if ($entity->getId() === null) {
            throw new DatabaseException('Cannot delete entity without id');
        }

        $sql = 'DELETE FROM ' . $this->getTableName() . ' WHERE id=:id';

        $this->dbQueryExecutor->execute($sql, [':id' => $entity->getId()]);
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    protected function insert(object $entity): void
    {
        $params = [];
        $placeholders = [];
        foreach ($this->dataMapper->mapEntityToRow($entity) as $column => $value) {
            if ($column === 'id') {
                continue;
            }
            $params[':' . $column] = $value;
            $placeholders[$column] = ':' . $column;
        }

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->getTableName(),
            implode(', ', array_keys($placeholders)),
            implode(', ', array_values($placeholders)),
        );

        $this->dbQueryExecutor->execute($sql, $params);

        $id = $this->dbQueryExecutor->getLastInsertId($this->getSequenceName());
        $this->dataMapper->hydrateId($entity, $id);
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    protected function update(object $entity): void
    {
        $params = [];
        $placeholders = [];
        foreach ($this->dataMapper->mapEntityToRow($entity) as $column => $value) {
            $params[':' . $column] = $value;
            $placeholders[$column] = $column . '=:' . $column;
        }

        $idPlaceholder = $placeholders['id'];
        unset($placeholders['id']);

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->getTableName(),
            implode(', ', $placeholders),
            $idPlaceholder,
        );

        $this->dbQueryExecutor->execute($sql, $params);
    }

    protected function createEntityFromRow(array $row): object
    {
        return $this->dataMapper->mapRowToEntity($row);
    }
}
