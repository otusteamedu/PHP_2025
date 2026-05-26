<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Repository;

use App\Domain\UserManagement\Collection\UserCollection;
use App\Infrastructure\Database\Connection\DatabaseQueryExecutor;
use App\Infrastructure\Database\DataMapper\DataMapperInterface;
use App\Infrastructure\Database\Factory\CollectionFactory;

class UserRepository extends AbstractRepository
{
    public function __construct(
        DatabaseQueryExecutor $dbQueryExecutor,
        DataMapperInterface $dataMapper,
        CollectionFactory $collectionFactory,
    ) {
        parent::__construct($dbQueryExecutor, $dataMapper, $collectionFactory);
    }

    protected function getTableName(): string
    {
        return 'users';
    }

    protected function getSequenceName(): string
    {
        return 'users_id_seq';
    }

    protected function getCollectionClassName(): string
    {
        return UserCollection::class;
    }
}
