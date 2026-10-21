<?php

declare(strict_types=1);

namespace App\Core\Database\Connection;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Core\Database\Exception\ConfigurationException;
use App\Core\Database\Exception\DatabaseException;

/**
 * Ленивое подключение к PostgreSQL.
 *
 * Соединение создаётся при первом обращении к getHandler() и живёт
 * до вызова reset(). Метод reset() предназначен для обработки
 * разрывов соединения в долгоживущих процессах (воркерах):
 * после сброса следующий getHandler() подключится заново.
 */
class PDOWrapper
{
    private ?\PDO $dbh = null;

    public function __construct(
        private readonly DotEnvConfigInterface $dotEnvConfig,
    ) {
    }

    /**
     * @throws DatabaseException
     * @throws ConfigurationException
     */
    public function getHandler(): \PDO
    {
        return $this->dbh ??= $this->connect();
    }

    public function reset(): void
    {
        $this->dbh = null;
    }

    /**
     * @throws DatabaseException
     * @throws ConfigurationException
     */
    private function connect(): \PDO
    {
        $host = $this->getRequiredCredential('POSTGRES_HOST');
        $dbName = $this->getRequiredCredential('POSTGRES_DB');
        $user = $this->getRequiredCredential('POSTGRES_USER');
        $password = $this->getRequiredCredential('POSTGRES_PASSWORD');

        try {
            $dsn = "pgsql:host=$host;dbname=$dbName";

            return new \PDO($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (\PDOException $e) {
            throw new DatabaseException(
                sprintf('Failed to connect to PostgreSQL: %s', $e->getMessage()),
                previous: $e,
            );
        }
    }

    /**
     * @throws ConfigurationException
     */
    private function getRequiredCredential(string $key): string
    {
        if (!$this->dotEnvConfig->has($key)) {
            throw new ConfigurationException(
                sprintf('Missing configuration credential: %s', $key),
            );
        }

        return (string) $this->dotEnvConfig->get($key);
    }
}
