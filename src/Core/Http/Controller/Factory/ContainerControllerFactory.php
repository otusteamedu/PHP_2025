<?php

declare(strict_types=1);

namespace App\Core\Http\Controller\Factory;

use App\Core\Container\Container;
use App\Core\Http\Controller\Base\ErrorController;
use App\Core\Http\ErrorHandler\ErrorHandlerInterface;

class ContainerControllerFactory implements ControllerFactoryInterface
{
    public function __construct(
        private readonly Container $container,
        private readonly ErrorHandlerInterface $errorHandler,
    ) {
    }

    public function createHttpController(string $className): object
    {
        if (!class_exists($className)) {
            throw new \Exception("Controller class '$className' does not exists.");
        }

        $reflection = new \ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $className();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            if ($type === null) {
                throw new \Exception("Parameter {$param->getName()} in $className has no type hint.");
            }
            $dependencies[] = $this->container->get($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    public function createErrorController(): ErrorController
    {
        return new ErrorController($this->errorHandler);
    }
}
