<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../config/bootstrap.php'; // Загрузка .env

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(require __DIR__ . '/../config/dependencies.php');
$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// ErrorMiddleware с кастомным обработчиком HttpNotFoundException
// По умолчанию Slim логирует 404 как ошибку с полным stack trace в php-errors.log,
$errorMiddleware = $app->addErrorMiddleware(
    (bool)($_ENV['APP_DEBUG'] ?? false),
    true,
    true,
);
$errorMiddleware->setErrorHandler(
    HttpNotFoundException::class,
    function (\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response) {
        return $response->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                (new StreamFactory())->createStream(json_encode(['error' => 'Not Found']))
            );
    }
);

$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

$app->run();
