<?php
declare(strict_types=1);

namespace App\Redis;

use Redis as Client;

class Redis
{
    private ?Client $client = null;

    public function __construct(private readonly Config $config)
    {
    }

    public function client(): Client
    {
        if (isset($this->client)) {
            return $this->client;
        }

        $host = $this->config->host;
        $port = $this->config->port;

        $client = new Client();
        $client->connect($host, $port);

        return $this->client = $client;
    }
}
