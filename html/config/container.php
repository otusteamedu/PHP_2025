<?php

declare(strict_types=1);

use League\Container\Container;
use League\Container\ReflectionContainer;
use Otus\Connections\Memcached;
use Otus\Connections\Postgres;
use Otus\Connections\Redis;

$container = new Container();
$container->delegate(new ReflectionContainer());

$container
    ->add(Redis::class, static function (): Redis {
        $connection = new Redis([
            'host' => getenv('REDIS_HOST'),
            'port' => (int) getenv('REDIS_PORT'),
        ]);

        $connection->select((int) getenv('REDIS_DATABASE'));

        return $connection;
    });

$container
    ->add(Memcached::class, static function (): Memcached {
        $connection = new Memcached();

        $connection->addServer(getenv('MEMCACHED_HOST'), (int) getenv('MEMCACHED_PORT'));

        return $connection;
    });

$container
    ->add(Postgres::class, static function (): Postgres {
        return new Postgres(getenv('PDO_DSN'), getenv('PDO_USERNAME'), getenv('PDO_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    });

return $container;
