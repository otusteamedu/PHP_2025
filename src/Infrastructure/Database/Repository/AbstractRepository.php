<?php

namespace App\Infrastructure\Database\Repository;

use App\Domain\Collection\AbstractCollection;
use App\Domain\Entity\EntityInterface;
use App\Infrastructure\Database\Connection\DatabaseQueryExecutor;
use App\Infrastructure\Database\DataMapper\DataMapperInterface;

abstract class AbstractRepository
{
    protected readonly DatabaseQueryExecutor $dbQueryExecutor;
    protected readonly DataMapperInterface $dataMapper;

    abstract protected function getTableName(): string;
    abstract protected function getSequenceName(): string;
    abstract protected function getDataMapperClassName(): string;
    abstract protected function getCollectionClassName(): string;

    public function __construct()
    {
        $this->dbQueryExecutor = new DatabaseQueryExecutor();
        $this->dataMapper = new ($this->getDataMapperClassName());
    }

    public function find(int $id): ?EntityInterface
    {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE id=:id';
        $row = $this->dbQueryExecutor->queryRow($sql, [':id' => $id]);

        return !empty($row) ? $this->createEntityFromRow($row) : null;
    }

    public function findAll(): AbstractCollection
    {
        $sql = 'SELECT * FROM ' . $this->getTableName();
        $rows = $this->dbQueryExecutor->queryRows($sql);

        /** @var AbstractCollection $collection */
        $collection = new ($this->getCollectionClassName());
        foreach ($rows as $row) {
            $collection->add($this->createEntityFromRow($row));
        }

        return $collection;
    }

    public function findAllPaginatedById(int $lastId, int $limit): AbstractCollection
    {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE id > :last_id ORDER BY id ASC LIMIT :limit';
        $rows = $this->dbQueryExecutor->queryRows($sql, [':last_id' => $lastId, ':limit' => $limit]);

        /** @var AbstractCollection $collection */
        $collection = new ($this->getCollectionClassName());
        foreach ($rows as $row) {
            $collection->add($this->createEntityFromRow($row));
        }

        return $collection;
    }

    public function save(EntityInterface $entity): EntityInterface
    {
        return $entity->getId() === null ? $this->insert($entity) : $this->update($entity);
    }

    public function delete(EntityInterface $entity): bool
    {
        $sql = 'DELETE FROM ' . $this->getTableName() . ' WHERE id=:id';

        return $this->dbQueryExecutor->execute($sql, [':id' => $entity->getId()]);
    }

    protected function insert(EntityInterface $entity): EntityInterface
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

        $result = $this->dbQueryExecutor->execute($sql, $params);
        if ($result === false) {
            throw new \RuntimeException('Не удалось добавить объект в БД.');
        }

        $id = $this->dbQueryExecutor->getLastInsertId($this->getSequenceName());
        $this->dataMapper->hydrateId($entity, $id);

        return $entity;
    }

    protected function update(EntityInterface $entity): EntityInterface
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

        $result = $this->dbQueryExecutor->execute($sql, $params);
        if ($result === false) {
            throw new \RuntimeException('Не удалось обновить объект в БД.');
        }

        return $entity;
    }

    protected function createEntityFromRow(array $row): EntityInterface
    {
        return $this->dataMapper->mapRowToEntity($row);
    }
}
