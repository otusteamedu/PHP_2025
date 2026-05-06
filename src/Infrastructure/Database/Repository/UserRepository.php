<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Repository;

use App\Domain\UserManagement\Collection\UserCollection;
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
