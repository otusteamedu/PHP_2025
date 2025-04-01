<?php

declare(strict_types=1);

namespace App\Storage\Redis;

use RuntimeException;

/**
 * Class Config
 * @package App\Storage\Redis
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
     *
     */
    public function __construct(array $config)
    {
        $this->ensureParams($config);

        $this->host = $config['redisHost'];
        $this->port = (int)$config['redisPort'];
    }

    /**
     * @param array $config
     * @return void
     */
    private function ensureParams(array $config): void
    {
        $expectedParams = [
            'redisHost',
            'redisPort',
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
}
