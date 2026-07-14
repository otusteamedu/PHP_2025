<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Repository;

use App\Core\Database\Repository\AbstractRepository;
use App\Domain\UserManagement\Collection\UserCollection;

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

    protected function getCollectionClassName(): string
    {
        return UserCollection::class;
    }
}
