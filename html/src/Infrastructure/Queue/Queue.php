<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Queue;

use Otus\Queue\Infrastructure\Queue\Adapter\AdapterInterface;

final class Queue implements QueueInterface
{
    /**
     * @var array
     */
    private array $connections = [];

    /**
     * @param array $config
     */
    public function __construct(
        private readonly array $config,
    ) {
    }

    /**
     * @param string $connection
     * @param Payload $payload
     */
    public function push(string $connection, Payload $payload): void
    {
        $this->getConnection($connection)->push($payload);
    }

    /**
     * @param string $connection
     * @param Payload $payload
     */
    public function pull(string $connection, Payload $payload): void
    {
        $this->getConnection($connection)->pull($payload);
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        return array_all(array_keys($this->config), fn (string $connection): bool => $this->getConnection($connection)->ping());
    }

    /**
     * @param string $connection
     *
     * @return AdapterInterface
     */
    private function getConnection(string $connection): AdapterInterface
    {
        if (array_key_exists($connection, $this->connections)) {
            return $this->connections[$connection];
        }

        [
            $connection => [
                'adapter' => $adapter,
                'config' => $config,
            ],
        ] = $this->config;

        return $this->connections[$connection] = new $adapter(...$config);
    }
}
