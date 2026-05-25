<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Container;
use App\Core\Http\App;
use App\Core\Http\Controller\Factory\ContainerControllerFactory;
use App\Core\Http\Controller\Factory\ControllerFactoryInterface;
use App\Core\Http\ErrorHandler\ErrorHandlerFactory;
use App\Core\Http\ErrorHandler\ErrorHandlerInterface;
use App\Core\Http\Message\Request;
use App\Core\Http\Routing\Router;
use App\Core\Http\View\View;
use App\Core\Utils\PathResolverInterface;

class HttpServiceProvider implements ServiceProviderInterface
{
    public function registerServices(Container $container): void
    {
        $container->set(Request::class, static fn() => new Request());

        $view = $container->has(View::class) ? $container->get(View::class) : null;
        $errorHandlerFactory = new ErrorHandlerFactory($view);
        $container->singleton(
            ErrorHandlerInterface::class,
            static fn(Container $c) => $errorHandlerFactory->create($c->get(Request::class)),
        );

        $container->singleton(
            ControllerFactoryInterface::class,
            static fn(Container $c) => new ContainerControllerFactory($c)
        );

        $container->singleton(
            Router::class,
            static fn(Container $c) => new Router(
                $c->get(ControllerFactoryInterface::class),
                $c->get(PathResolverInterface::class),
            ),
        );

        $container->set(
            App::class,
            static fn(Container $c) => new App($c->get(Request::class), $c->get(Router::class)),
        );
    }
}
