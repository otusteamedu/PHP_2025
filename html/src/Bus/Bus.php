<?php

declare(strict_types=1);

namespace Otus\DataMapper\Bus;

use Otus\DataMapper\Di\Container;
use Otus\DataMapper\Di\UnresolveParameterException;
use ReflectionException;

final class Bus
{
    /**
     * @param array $handlers
     */
    public function __construct(private array $handlers = [])
    {
    }

    /**
     * @param string $command
     * @param callable $handler
     */
    public function register(string $command, callable $handler): void
    {
        $this->handlers[$command] = $handler;
    }

    /**
     * @param string $command
     */
    public function unregister(string $command): void
    {
        unset($this->handlers[$command]);
    }

    /**
     * @param string $command
     *
     * @return bool
     */
    public function has(string $command): bool
    {
        return array_key_exists($command, $this->handlers);
    }

    /**
     * @param string $command
     * @param array $args
     *
     * @return int
     *
     * @throws UnresolveParameterException
     * @throws ReflectionException
     */
    public function handle(string $command, array $args = []): int
    {
        $handler = $this->handlers[$command];

        if (is_string($handler)) {
            $handler = Container::getInstance()->get($handler);
        }

        return $handler(...$args);
    }
}
