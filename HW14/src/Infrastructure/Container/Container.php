<?php
declare(strict_types=1);

namespace App\Infrastructure\Container;

class Container
{
    private array $definitions = [];

    private array $instances = [];

    public function set(string $id, $definition): void
    {
        $this->definitions[$id] = $definition;
        unset($this->instances[$id]);
    }

    public function get($id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->definitions[$id])) {
            throw new \Exception("Service {$id} not found in container");
        }

        $definition = $this->definitions[$id];

        if (is_callable($definition)) {
            $instance = $definition($this);
        } elseif (is_string($definition) && class_exists($definition)) {
            $instance = new $definition();
        } else {
            $instance = $definition;
        }

        $this->instances[$id] = $instance;
        return $instance;
    }

    public function has($id): bool
    {
        return isset($this->definitions[$id]);
    }

    public function singleton(string $id, $definition): void
    {
        $this->set($id, function (Container $container) use ($definition) {
            static $instance;

            if ($instance === null) {
                if (is_callable($definition)) {
                    $instance = $definition($container);
                } elseif (is_string($definition) && class_exists($definition)) {
                    $instance = new $definition();
                } else {
                    $instance = $definition;
                }
            }

            return $instance;
        });
    }
}
