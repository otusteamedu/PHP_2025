<?php

declare(strict_types=1);

namespace App\Storage\Redis;

use Redis;
use RedisException;

/**
 * Class ClientBuilder
 * @package App\Storage\Redis
 */
class ClientBuilder
{
    /**
     * @param Config $config
     * @return Redis
     * @throws RedisException
     */
    public static function create(Config $config): Redis
    {
        $redis = new Redis([
            'host' => $config->getHost(),
            'port' => $config->getPort(),
        ]);
        $redis->ping();

        return $redis;
    }
}
