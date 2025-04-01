<?php

declare(strict_types=1);

namespace App\Storage\Elastic;

use RuntimeException;

/**
 * Class Config
 * @package App\Storage\Elastic
 */
class Config
{
    /**
     * @var string
     */
    private string $host;
    /**
     * @var string
     */
    private string $port;
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
    private string $storageName;
    /**
     * @var array
     */
    private array $storageSettings;

    /**
     *
     */
    public function __construct(array $config)
    {
        $this->ensureParams($config);

        $this->host = $config['elasticHost'];
        $this->port = $config['elasticPort'];
        $this->userName = $config['elasticUsername'];
        $this->password = $config['elasticPassword'];
        $this->storageName = $config['elasticStorageName'];
        $this->storageSettings = $config['elasticStorageSettings'];
    }

    /**
     * @param array $config
     * @return void
     */
    private function ensureParams(array $config): void
    {
        $expectedParams = [
            'elasticHost',
            'elasticPort',
            'elasticUsername',
            'elasticPassword',
            'elasticStorageName',
            'elasticStorageSettings',
        ];

        foreach ($expectedParams as $param) {
            if (!isset($config[$param])) {
                throw new RuntimeException("Elasticsearch config param '$param' not set");
            }
        }

        if (!is_array($config['elasticStorageSettings'])) {
            throw new RuntimeException('Elasticsearch storage settings configuration must be an array');
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
     * @return string
     */
    public function getPort(): string
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
    public function getStorageName(): string
    {
        return $this->storageName;
    }

    /**
     * @return array
     */
    public function getStorageSettings(): array
    {
        return $this->storageSettings;
    }
}
