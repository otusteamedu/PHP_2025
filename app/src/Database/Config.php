<?php

declare(strict_types=1);

namespace App\Database;

use RuntimeException;

/**
 * Class Config
 * @package App\Database
 */
class Config
{
    /**
     * @var string
     */
    private string $host;
    /**
     * @var int
     */
    private int $port;
    /**
     * @var string
     */
    private string $userName;
    /**
     * @var string
     */
    private string $password;
    /**
     * @var string
     */
    private string $dbName;

    /**
     *
     */
    public function __construct(array $config)
    {
        $this->ensureParams($config);

        $this->host = $config['postgresHost'];
        $this->port = (int)$config['postgresPort'];
        $this->userName = $config['postgresUsername'];
        $this->password = $config['postgresPassword'];
        $this->dbName = $config['postgresDbName'];
    }

    /**
     * @param array $config
     * @return void
     */
    private function ensureParams(array $config): void
    {
        $expectedParams = [
            'postgresHost',
            'postgresPort',
            'postgresUsername',
            'postgresPassword',
            'postgresDbName',
        ];

        foreach ($expectedParams as $param) {
            if (!isset($config[$param])) {
                throw new RuntimeException("Param '$param' not set");
            }
        }
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->dbName;
    }
}
