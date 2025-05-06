<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionClass;
use ReflectionException;
use RuntimeException;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    /**
     * @param string $abstract
     * @param callable $factory
     * @param bool $singleton
     * @return void
     */
    public function bind(string $abstract, callable $factory, bool $singleton = false): void
    {
        $this->bindings[$abstract] = [
            'factory' => $factory,
            'singleton' => $singleton
        ];
    }

    /**
     * @param string $abstract
     * @param callable $factory
     * @return void
     */
    public function singleton(string $abstract, callable $factory): void
    {
        $this->bind($abstract, $factory, true);
    }

    /**
     * @throws ReflectionException
     */
    public function make(string $abstract)
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (isset($this->bindings[$abstract])) {
            $result = $this->bindings[$abstract]['factory']($this);

            // Если это синглтон - сохраняем экземпляр
            if ($this->bindings[$abstract]['singleton']) {
                $this->instances[$abstract] = $result;
            }

            return $result;
        }

        if (!class_exists($abstract)) {
            throw new RuntimeException("Class {$abstract} does not exist");
        }

        $reflector = new ReflectionClass($abstract);

        if (!$reflector->isInstantiable()) {
            throw new RuntimeException("Class {$abstract} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $abstract();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (!$type || $type->isBuiltin()) {
                throw new RuntimeException(
                    "Cannot resolve parameter {$parameter->getName()} in class {$abstract}"
                );
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}