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
use App\Core\Http\Message\Request;
use App\Core\Http\Routing\Router;
use App\Core\Http\View\View;
use App\Core\Utils\PathResolverInterface;

class HttpServiceProvider implements ServiceProviderInterface
{
    public function registerServices(Container $container): void
    {
        // Получаем определитель контектста для обработчика ошибок
        $contextDetector = $container->get(ContextDetector::class);

        // Создаем обработчик ошибок для фабрики контроллеров
        $view = $container->has(View::class) ? $container->get(View::class) : null;
        $errorHandler = $this->createErrorHandler($contextDetector, $view);

        // Создаем фабрику контроллеров для роутера
        $controllerFactory = new ContainerControllerFactory($container, $errorHandler);

        // Регистрируем сервисы
        $this->registerRouter($container, $controllerFactory);
        $this->registerRequest($container);
        $this->registerApp($container);
    }

    private function createErrorHandler(ContextDetector $detector, ?View $view): ErrorHandlerInterface
    {
        $factory = new ErrorHandlerFactory($detector, $view);

        return $factory->create();
    }

    private function registerRouter(Container $container, ControllerFactoryInterface $factory): void
    {
        $container->singleton(
            Router::class,
            static fn(Container $c) => new Router($factory, $c->get(PathResolverInterface::class))
        );
    }

    private function registerRequest(Container $container): void
    {
        $container->set(Request::class, static fn() => new Request());
    }

    private function registerApp(Container $container): void
    {
        $container->set(App::class, static fn(Container $c) => new App($c->get(Request::class), $c->get(Router::class)));
    }
}
