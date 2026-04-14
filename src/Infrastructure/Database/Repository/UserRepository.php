<?php

namespace App\Infrastructure\Database\Repository;

use App\Domain\Collection\UserCollection;
use App\Infrastructure\Database\DataMapper\UserMapper;

class UserRepository extends AbstractRepository
{
    protected function getTableName(): string
    {
        return 'users';
    }

    protected function getSequenceName(): string
    {
        return 'users_id_seq';
    }

    protected function getDataMapperClassName(): string
    {
        return UserMapper::class;
    }

    protected function getCollectionClassName(): string
    {
        return UserCollection::class;
    }
}
