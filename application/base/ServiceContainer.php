<?php

namespace App\Base;

use App\Base\Exceptions\ServiceContainer\ServiceContainerException;
use App\Base\Exceptions\ServiceContainer\ServiceContainerNotFoundException;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;

final class ServiceContainer implements ContainerInterface
{
    private static ?self $instance = null;
    private array $definitions = [];
    private array $instances = [];

    private array $inProgress = [];

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function set(string $id, ?callable $factory = null): void
    {
        if ($this->has($id)) {
            throw new ServiceContainerException("Service {$id} already registered in container");
        } elseif (!class_exists($id)) {
            throw new ServiceContainerException("Service {$id} does not exist");
        }

        $this->definitions[$id] = $factory;
    }

    public function has(string $id): bool
    {
        if ($this->definitions[$id] ?? false) {
            return true;
        }
        return false;
    }

    public function get(string $id): object
    {
        if (!$this->has($id)) {
            throw new ServiceContainerNotFoundException("Service '$id' not found");
        }
        $instance = $this->instances[$id] ?? false;
        if (!$instance) {
            try {
                $factory = $this->definitions[$id];
                if ($factory) {
                    $instance = $this->call($factory);
                } else {
                    $instance = $this->make($id);
                }
            } catch (Throwable $e) {
                throw new ServiceContainerException("Failed to resolve the service '$id'.", 0, $e);
            }
        }

        return $instance;
    }

    /**
     * @throws ReflectionException
     */
    public function call(callable $callable, array $parameters = []): mixed
    {
        $passedParameters = array_keys($parameters);
        try {
            $params = $this->getParams($callable, $passedParameters);
            foreach ($params as $paramName => $param) {
                $params[$paramName] = $this->make($param);
            }
        } catch (Throwable $e) {
            $callableString = var_export($callable, true);
            throw new ServiceContainerException("Failed to build arguments for '$callableString'.", 500, $e);
        }
        return call_user_func($callable, ...$params);
    }

    /**
     * @return array<string, class-string>
     * @throws ReflectionException
     */
    private function getParams(string|callable $callable, array $passedParameters = []): array
    {
        if (is_string($callable)) {
            $reflectionClass = new ReflectionClass($callable);
            $reflectionCallable = $reflectionClass->getConstructor();
            if (!$reflectionCallable) {
                return [];
            }
        } elseif (is_object($callable)) {
            $reflectionCallable = new ReflectionMethod($callable, '__invoke');
        } elseif (is_array($callable)) {
            [$clsOrObject, $methodName] = $callable;
            $reflectionCallable = new ReflectionMethod($clsOrObject, $methodName);
        } elseif (is_callable($callable)) {
            $reflectionCallable = ReflectionMethod::createFromMethodName($callable);
        } else {
            throw new ServiceContainerException("The '$callable' method does not exist");
        }

        $params = [];
        foreach ($reflectionCallable->getParameters() as $param) {
            $paramName = $param->getName();
            if (in_array($paramName, $passedParameters)) {
                $params[$paramName] = $passedParameters;
                unset($passedParameters[$paramName]);
                continue;
            } elseif ($param->isDefaultValueAvailable()) {
                continue;
            } elseif ($param->getType()->isBuiltin()) {
                throw new ServiceContainerException(
                    "Can't resolve the parameter '{$param->getType()} {$paramName}' of '{$callable}'"
                );
            }
            $params[$paramName] = $param->getType()->getName();
        }
        if ($passedParameters) {
            throw new InvalidArgumentException("Invalid parameters (" . implode(', ', $passedParameters) . ") passed");
        }

        return $params;
    }

    /**
     * @param class-string $class
     * @return object $class
     */
    public function make(string $class): object
    {
        try {
            if (!class_exists($class)) {
                throw new ServiceContainerException("Service '{$class}' does not exist");
            } elseif ($this->inProgress[$class] ?? false) {
                throw new ServiceContainerException("Detected loop dependency '$class'");
            } elseif ($this->has($class)) {
                return $this->get($class);
            }
            $this->inProgress[$class] = true;

            $params = $this->getParams($class);
            foreach ($params as $paramName => $param) {
                $params[$paramName] = $this->make($param);
            }
        } catch (Throwable $e) {
            throw new ServiceContainerException("Failed to resolve arguments for '{$class}'.", 0, $e);
        }
        unset($this->inProgress[$class]);

        return new $class(...$params);
    }
}