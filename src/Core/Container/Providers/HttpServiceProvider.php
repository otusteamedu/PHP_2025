<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Container;
use App\Core\Container\Context\ContextDetector;
use App\Core\Http\App;
use App\Core\Http\Controller\Factory\ContainerControllerFactory;
use App\Core\Http\Controller\Factory\ControllerFactoryInterface;
use App\Core\Http\ErrorHandler\ErrorHandlerFactory;
use App\Core\Http\ErrorHandler\ErrorHandlerInterface;
use App\Core\Http\ErrorHandler\ExceptionResponseResolverInterface;
use App\Core\Http\Message\Request;
use App\Core\Http\Routing\Router;
use App\Core\Http\View\View;
use App\Core\Resolver\ConstructorDependencyResolverInterface;
use App\Core\Utils\PathResolverInterface;

class HttpServiceProvider extends AbstractServiceProvider
{
    protected function doRegisterServices(Container $container): void
    {
        $this->registerErrorHandlerFactory($container);
        $this->registerErrorHandler($container);
        $this->registerControllerFactory($container);
        $this->registerRouter($container);
        $this->registerRequest($container);
        $this->registerApp($container);
    }

    private function registerErrorHandlerFactory(Container $container): void
    {
        $container->singleton(
            ErrorHandlerFactory::class,
            static fn(Container $c) => new ErrorHandlerFactory(
                $c->get(ContextDetector::class),
                $c->has(View::class) ? $c->get(View::class) : null,
                $c->has(ExceptionResponseResolverInterface::class)
                    ? $c->get(ExceptionResponseResolverInterface::class)
                    : null,
            ),
        );
    }

    private function registerErrorHandler(Container $container): void
    {
        $container->singleton(
            ErrorHandlerInterface::class,
            static fn(Container $c) => $c->get(ErrorHandlerFactory::class)->create(),
        );
    }

    private function registerControllerFactory(Container $container): void
    {
        $container->singleton(
            ControllerFactoryInterface::class,
            static fn(Container $c) => new ContainerControllerFactory(
                $c,
                $c->get(ConstructorDependencyResolverInterface::class),
            ),
        );
    }

    private function registerRouter(Container $container): void
    {
        $container->singleton(
            Router::class,
            static fn(Container $c) => new Router(
                $c->get(ControllerFactoryInterface::class),
                $c->get(PathResolverInterface::class),
            )
        );
    }

    private function registerRequest(Container $container): void
    {
        $container->set(Request::class, static fn() => Request::fromGlobals());
    }

    private function registerApp(Container $container): void
    {
        $container->set(
            App::class,
            static fn(Container $c) => new App(
                $c->get(Request::class),
                $c->get(Router::class),
                $c->get(ErrorHandlerInterface::class),
            ),
        );
    }
}
