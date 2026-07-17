<?php

declare(strict_types=1);

namespace App\Core\Http\Controller\Factory;

use App\Core\Container\Container;
use App\Core\Http\Controller\Base\ErrorController;
use App\Core\Http\ErrorHandler\ErrorHandlerInterface;
use App\Core\Resolver\ConstructorDependencyResolverInterface;

class ContainerControllerFactory implements ControllerFactoryInterface
{
    public function __construct(
        private readonly Container $container,
        private readonly ErrorHandlerInterface $errorHandler,
        private readonly ConstructorDependencyResolverInterface $resolver,
    ) {
    }

    public function createHttpController(string $className): object
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Controller class '$className' does not exists.");
        }

        $deps = $this->resolver->resolve($className);

        $resolved = [];
        foreach ($deps as $depName) {
            $resolved[] = $this->container->get($depName);
        }

        return new $className(...$resolved);
    }

    public function createErrorController(): ErrorController
    {
        return new ErrorController($this->errorHandler);
    }
}
