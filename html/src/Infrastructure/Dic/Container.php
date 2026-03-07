<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Dic;

use ReflectionClass;
use ReflectionException;

final class Container
{
    /**
     * @var array
     */
    private array $instances = [];

    /**
     * @var array
     */
    private array $singletons = [];

    /**
     * @var array
     */
    private array $definitions = [];

    /**
     * @var Container|null
     */
    private static ?Container $instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return Container
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $class
     *
     * @return object
     *
     * @throws ReflectionException
     * @throws UnresolveParameterException
     */
    public function get(string $class): object
    {
        if (array_key_exists($class, $this->instances)) {
            return $this->instances[$class];
        }

        if (array_key_exists($class, $this->singletons)) {
            $callback = $this->singletons[$class];

            $this->instances[$class] = $callback($this);

            return $this->instances[$class];
        }

        if (array_key_exists($class, $this->definitions)) {
            $callback = $this->definitions[$class];

            return $callback($this);
        }

        return $this->resolve($class);
    }

    /**
     * @param string $class
     * @param callable $callback
     */
    public function setSingleton(string $class, callable $callback): void
    {
        $this->singletons[$class] = $callback;
    }

    /**
     * @param string $class
     * @param callable $callback
     */
    public function setDefinition(string $class, callable $callback): void
    {
        $this->definitions[$class] = $callback;
    }

    /**
     * @param string $class
     *
     * @return object
     *
     * @throws ReflectionException
     * @throws UnresolveParameterException
     */
    private function resolve(string $class): object
    {
        $reflector = new ReflectionClass($class);

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $parameters = $constructor->getParameters();

        $dependencies = [];

        foreach ($parameters as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                if ($parameter->hasType() && !$parameter->getType()->isBuiltin()) {
                    $dependencies[] = $this->get($parameter->getType()->getName());
                } else {
                    throw new UnresolveParameterException($parameter);
                }
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
