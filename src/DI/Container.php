<?php

declare(strict_types=1);

namespace Restaurant\DI;

use Exception;
use ReflectionClass;
use ReflectionParameter;

class Container
{
    private array $services = [];
    private array $instances = [];

    public function register(): void
    {
        // Регистрируем билдеры
        $this->set(\Restaurant\Builder\BurgerBuilder::class, fn() => new \Restaurant\Builder\BurgerBuilder());
        $this->set(\Restaurant\Builder\SandwichBuilder::class, fn() => new \Restaurant\Builder\SandwichBuilder());
        $this->set(\Restaurant\Builder\HotDogBuilder::class, fn() => new \Restaurant\Builder\HotDogBuilder());

        // Регистрируем команды
        $this->set(
            \Restaurant\Command\CreateBurgerCommand::class,
            fn() => new \Restaurant\Command\CreateBurgerCommand($this->get(\Restaurant\Builder\BurgerBuilder::class))
        );

        $this->set(
            \Restaurant\Command\CreateCustomBurgerCommand::class,
            fn() => new \Restaurant\Command\CreateCustomBurgerCommand($this->get(\Restaurant\Builder\BurgerBuilder::class))
        );

        $this->set(
            \Restaurant\Command\CreatePizzaCommand::class,
            fn() => new \Restaurant\Command\CreatePizzaCommand()
        );

        $this->set(
            \Restaurant\Command\ProcessOrderCommand::class,
            fn() => new \Restaurant\Command\ProcessOrderCommand($this->get(\Restaurant\Builder\BurgerBuilder::class))
        );

        $this->set(
            \Restaurant\Command\QualityCheckCommand::class,
            fn() => new \Restaurant\Command\QualityCheckCommand($this->get(\Restaurant\Builder\BurgerBuilder::class))
        );
    }

    public function set(string $id, callable $factory): void
    {
        $this->services[$id] = $factory;
    }

    public function get(string $id): object
    {
        if (!isset($this->instances[$id])) {
            if (isset($this->services[$id])) {
                $this->instances[$id] = $this->services[$id]($this);
            } else {
                $this->instances[$id] = $this->autowire($id);
            }
        }

        return $this->instances[$id];
    }

    private function autowire(string $className): object
    {
        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $className();
        }

        $parameters = $constructor->getParameters();
        $dependencies = array_map(
            fn(ReflectionParameter $param) => $this->resolveDependency($param),
            $parameters
        );

        return $reflectionClass->newInstanceArgs($dependencies);
    }

    private function resolveDependency(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if ($type === null) {
            throw new Exception("Невозможно разрешить зависимость для {$parameter->getName()}");
        }

        $typeName = $type->getName();

        if (class_exists($typeName) || interface_exists($typeName)) {
            return $this->get($typeName);
        }

        throw new Exception("Невозможно разрешить зависимость типа {$typeName}");
    }
}
