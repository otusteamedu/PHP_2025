<?php

namespace Arlex2305k\Redis\Storage;

use Dotenv\Dotenv;

class StorageFactory
{
    public static function createStorage(string $type = 'redis'): StorageInterface
    {
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
		$dotenv->load();

        switch ($type) {
            case 'redis':
                $redis = new \Redis();
                $redisHost = $_ENV['REDIS_HOST'] ?? 'redis';
                $redisPort = $_ENV['REDIS_PORT'] ?? 6379;
                try {
                    $redis->connect($redisHost, $redisPort);
                    $redis->select(0);
                } catch (Exception $e) {
                    throw new Exception("Не удалось подключиться к Redis: " . $e->getMessage());
                }
                return new RedisStorage($redis);

            case 'memcached':
                $memcached = new \Memcached();
                $memcachedHost = $_ENV['MEMCACHED_HOST'] ?? 'memcached';
                $memcachedPort = $_ENV['MEMCACHED_PORT'] ?? 11211;
                $memcached->addServer($memcachedHost, $memcachedPort);
                $memcached->getVersion();
                return new MemcachedStorage($memcached);

            default:
                throw new \InvalidArgumentException("Неподдерживаемый тип хранилища: {$type}");
        }
    }
}
