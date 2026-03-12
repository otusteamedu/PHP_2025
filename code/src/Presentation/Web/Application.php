<?php

declare(strict_types=1);

namespace Queues\Presentation\Web;

use DI\Container;
use Queues\Config\ContainerConfig;
use Queues\Application\Handlers\ErrorHandler;
use Queues\Application\Interfaces\RequestInterface;
use Queues\Application\Interfaces\ResponseInterface;
use Queues\Presentation\Controllers\StatementsController;

class Application
{
    private Container $container;
    private Router $router;

    public function __construct()
    {
        $this->container = ContainerConfig::getContainer();
        $this->router = $this->container->get(Router::class);

        $this->setupErrorHandler();
        $this->registerRoutes();
    }

    public function run(): void
    {
        $this->router->run(
            $this->container->get(RequestInterface::class),
            $this->container->get(ResponseInterface::class)
        );
    }

    private function setupErrorHandler(): void
    {
        $errorHandler = $this->container->get(ErrorHandler::class);
        set_exception_handler([$errorHandler, 'handle']);
    }

    private function registerRoutes(): void
    {
        $controller = $this->container->get(StatementsController::class);
        $controller->registerRoutes($this->router);
    }
}
