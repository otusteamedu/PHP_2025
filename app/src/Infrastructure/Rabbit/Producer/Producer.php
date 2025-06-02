<?php

declare(strict_types=1);

namespace App\Infrastructure\Rabbit\Producer;

use App\Application\DTO\BankStatementMessage;
use App\Application\Producer\ProducerInterface;
use App\Infrastructure\Rabbit\Client;
use App\Infrastructure\Rabbit\Config;
use Exception;

/**
 * Class Producer
 * @package App\Infrastructure\Rabbit\Producer
 */
readonly class Producer implements ProducerInterface
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
     * @param BankStatementMessage $message
     * @return void
     */
    public function publish(BankStatementMessage $message): void
    {
        $this->client->publish((string)$message);
    }
}
