<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Dinargab\Homework20\Infrastructure\Http\JobsController;
use Dinargab\Homework20\Infrastructure\Http\StatementController;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/definitions.php');
$container = $containerBuilder->build();
$app = Bridge::create($container);
$app->addBodyParsingMiddleware();


$app->group('/api/v1', function (RouteCollectorProxy $group) {
    $group->get('/jobs', [JobsController::class, 'index']);
    $group->get('/jobs/{id:[0-9]+}', [JobsController::class, 'getJobStatus']);
    $group->post("/statement", [StatementController::class, 'store']);
    $group->get('/statement/{id:[0-9]+}', [StatementController::class, 'getStatement'])->setName('statement');
});

$app->run();
