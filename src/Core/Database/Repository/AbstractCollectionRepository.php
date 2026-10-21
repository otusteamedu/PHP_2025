<?php

declare(strict_types=1);

namespace App\Core\Database\Repository;

use App\Core\Database\Collection\AbstractCollection;
use App\Core\Database\Connection\DatabaseQueryExecutor;
use App\Core\Database\DataMapper\DataMapperInterface;
use App\Core\Database\DataMapper\IdentityMapperInterface;
use App\Core\Database\Exception\ConfigurationException;
use App\Core\Database\Exception\DatabaseException;
use App\Core\Database\Factory\CollectionFactory;

abstract class AbstractCollectionRepository extends AbstractRepository
{
    public function __construct(
        DatabaseQueryExecutor $dbQueryExecutor,
        DataMapperInterface&IdentityMapperInterface $dataMapper,
        protected readonly CollectionFactory $collectionFactory,
    ) {
        parent::__construct($dbQueryExecutor, $dataMapper);
    }

    abstract protected function getCollectionClassName(): string;

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    public function findAllAsCollection(): AbstractCollection
    {
        return $this->toCollection($this->findAll());
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    public function findAllPaginatedById(int $lastId, int $limit): AbstractCollection
    {
        $sql = 'SELECT * FROM ' . $this->getTableName()
            . ' WHERE id > :last_id ORDER BY id ASC LIMIT :limit';

        return $this->toCollection(
            $this->dbQueryExecutor->queryRows($sql, [':last_id' => $lastId, ':limit' => $limit]),
        );
    }

    private function toCollection(array $rows): AbstractCollection
    {
        $collection = $this->collectionFactory->create($this->getCollectionClassName());
        foreach ($rows as $row) {
            $collection->add($this->createEntityFromRow($row));
        }

        return $collection;
    }
}
