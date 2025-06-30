<?php

declare(strict_types=1);

namespace App\Infrastructure\DataBase;

use Doctrine\DBAL\Connection;

final class DataBaseConnection
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }
}
