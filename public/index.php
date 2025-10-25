<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Config\AppConfig;
use App\Infrastructure\Container\ServiceContainer;
use App\Infrastructure\ErrorHandler\ErrorHandler;

AppConfig::load();
ErrorHandler::register();

try {
    $container = ServiceContainer::getInstance();
    $router = $container->getApiRouter();
    $router->handle();
} catch (Exception $e) {
    ErrorHandler::handleException($e);
}
