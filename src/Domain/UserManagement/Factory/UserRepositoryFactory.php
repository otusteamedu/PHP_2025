<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Factory;

use App\Core\Database\Connection\DatabaseQueryExecutor;
use App\Core\Database\Factory\CollectionFactory;
use App\Infrastructure\Database\DataMapper\UserMapper;
use App\Infrastructure\Database\Repository\UserRepository;

class UserRepositoryFactory
{
    public function __construct(
        private readonly DatabaseQueryExecutor $executor,
        private readonly CollectionFactory $factory,
    ) {
    }

    public function create(UserMapper $mapper): UserRepository
    {
        return new UserRepository($this->executor, $this->factory, $mapper);
    }
}
