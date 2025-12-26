<?php

declare(strict_types=1);

namespace App\RedisClient;

use Dotenv\Dotenv;
use Exception;
use Predis\Client;
use RuntimeException;

class RedisConnect
{
    private Client $client;
    public function __construct()
    {
        Dotenv::createImmutable(__DIR__ . '/../..')->load();

        $this->client = new Client([
            'scheme'   => $_ENV['SCHEME'],
            'host'     => $_ENV['HOST'],
            'port'     => $_ENV['PORT'],
            'database' => $_ENV['DATABASE'],
        ]);
    }

    public function connect(): Client
    {
        try {
            $this->client->connect();
        } catch (Exception $e) {
            throw new RuntimeException("Redis connection failed: " . $e->getMessage(), 0, $e);
        }

        return $this->client;
    }
}
