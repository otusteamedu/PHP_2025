<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Redis;

use Predis\Client;

class RedisClient
{

    private const SCHEME = 'tcp';
    private const HOST = 'redis';
    private const PORT = '6379';

    private Client $client;

    public function __construct(string $host = self::HOST)
    {
        $this->client = new Client([
            'scheme' => self::SCHEME,
            'host' => $host,
            'port' => self::PORT,
        ]);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}