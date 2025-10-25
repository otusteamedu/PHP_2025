<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Container\ServiceContainer;
use App\Infrastructure\ErrorHandler\ErrorHandler;

try {
    $container = ServiceContainer::getInstance();
    $router = $container->getRouter();

    $router->handle($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (Throwable $exception) {
    $errorHandler = new ErrorHandler();
    $errorHandler->handle($exception);
}
