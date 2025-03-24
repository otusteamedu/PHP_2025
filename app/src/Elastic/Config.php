<?php declare(strict_types=1);

namespace App\Elastic;

use RuntimeException;

/**
 * Class Config
 * @package App\Elastic
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
        $this->storageName = $config['storageName'];
        $this->storageSettings = $config['storageSettings'];
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
            'storageName',
            'storageSettings',
        ];

        foreach ($expectedParams as $param) {
            if (!isset($config[$param])) {
                throw new RuntimeException("Param '$param' not set");
            }
        }

        if (!is_array($config['storageSettings'])) {
            throw new RuntimeException('Storage settings configuration must be an array');
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
