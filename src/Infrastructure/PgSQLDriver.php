<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Service\DotEnvLoader;

class PgSQLDriver
{
    private readonly DotEnvLoader $dotEnvLoader;
    private readonly \PDO $dbh;

    /**
     * @throws \PDOException
     */
    public function __construct()
    {
        $this->dotEnvLoader = new DotEnvLoader();
        $this->dbh = new \PDO(
            $this->getDsn(),
            $this->dotEnvLoader->getEnv('POSTGRES_USER'),
            $this->dotEnvLoader->getEnv('POSTGRES_PASSWORD')
        );
    }

    public function getVersion(): string
    {
        return $this->dbh->query('SELECT version();')->fetch(\PDO::FETCH_COLUMN);
    }

    private function getDsn(): string
    {
        return "pgsql:host=postgres;dbname={$this->dotEnvLoader->getEnv('POSTGRES_DB')}";
    }
}
