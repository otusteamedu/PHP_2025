<?php

declare(strict_types=1);

namespace App\Infrastructure\Rabbit\Consumer;

use App\Application\Consumer\ConsumerInterface;
use App\Infrastructure\Rabbit\Client;
use App\Infrastructure\Rabbit\Config;
use ErrorException;
use Exception;

/**
 * Class Consumer
 * @package App\Infrastructure\Rabbit\Consumer
 */
readonly class Consumer implements ConsumerInterface
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @param Config $config
     * @throws Exception
     */
    public function __construct(Config $config)
    {
        $this->client = new Client($config);
    }

    /**
     * @param callable $callback
     * @return void
     * @throws ErrorException
     */
    public function consume(callable $callback): void
    {
        $this->client->consume($callback);
    }
}
