<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

use RuntimeException;

class Container
{
    private array $definitions;
    private array $instances = [];

    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    public function get(string $id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->definitions[$id])) {
            throw new RuntimeException("Service '$id' not defined");
        }

        $factory = $this->definitions[$id];

        $object = $factory($this);

        $this->instances[$id] = $object;

        return $object;
    }
}
