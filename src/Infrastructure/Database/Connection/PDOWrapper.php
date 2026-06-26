<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Connection;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;

class PDOWrapper
{
    private ?\PDO $dbh = null;

    public function __construct(
        private readonly DotEnvConfigInterface $dotEnvConfig,
    ) {
    }

    public function getHandler(): \PDO
    {
        if ($this->dbh === null) {
            $host = $this->getRequiredCredential('POSTGRES_HOST');
            $dbName = $this->getRequiredCredential('POSTGRES_DB');
            $user = $this->getRequiredCredential('POSTGRES_USER');
            $password = $this->getRequiredCredential('POSTGRES_PASSWORD');

            try {
                $this->dbh = new \PDO("pgsql:host=$host;dbname=$dbName", $user, $password);
                $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                throw new \RuntimeException("Failed to connect to PostgreSQL: " . $e->getMessage());
            }
        }

        return $this->dbh;
    }

    private function getRequiredCredential(string $key): mixed
    {
        if (!$this->dotEnvConfig->has($key)) {
            throw new \RuntimeException("Missing configuration credential: $key");
        }

        return $this->dotEnvConfig->get($key);
    }
}
