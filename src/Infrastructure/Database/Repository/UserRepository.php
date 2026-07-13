<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Repository;

use App\Core\Database\Connection\DatabaseQueryExecutor;
use App\Core\Database\DataMapper\DataMapperInterface;
use App\Core\Database\Factory\CollectionFactory;
use App\Core\Database\Repository\AbstractRepository;
use App\Domain\UserManagement\Collection\UserCollection;

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
