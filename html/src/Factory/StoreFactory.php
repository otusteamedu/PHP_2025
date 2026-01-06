<?php

declare(strict_types=1);

namespace Otus\Cache\Factory;

use Memcached;
use Otus\Cache\Adapter\AdapterInterface;
use Otus\Cache\Adapter\PhpMemcachedAdapter;
use Otus\Cache\Adapter\PhpRedisAdapter;
use Redis;

final class StoreFactory
{
    /**
     * @return AdapterInterface
     */
    public static function factory(): AdapterInterface
    {
        return match (env('STORE')) {
            'memcached' => (static function (): PhpMemcachedAdapter {
                $driver = new Memcached();

                $driver->addServers([
                    [env('MEMCACHED_HOST', 'localhost'), (int) env('MEMCACHED_PORT', 11211)],
                ]);

                return new PhpMemcachedAdapter($driver);
            })(),

            default => (static function (): PhpRedisAdapter {
                $driver = new Redis();

                $driver->connect(env('REDIS_HOST', 'localhost'), (int) env('REDIS_PORT', 6379));

                return new PhpRedisAdapter($driver);
            })(),
        };
    }
}
