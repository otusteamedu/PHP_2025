<?php

declare(strict_types=1);

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Container\Container;
use League\Route\Router;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES,
);

if (file_exists(__DIR__ . '/../.env')) {
    new Dotenv()
        ->usePutenv()
        ->load(__DIR__ . '/../.env');
} else {
    echo 'Environment configuration file ".env" is missing.';
    exit(1);
}

/** @var Container $container */
$container = require __DIR__ . '/../config/container.php';

/** @var Router $router */
$router = require __DIR__ . '/../config/routes.php';

$response = $router->handle($request);
new SapiEmitter()->emit($response);
