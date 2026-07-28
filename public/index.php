<?php

declare(strict_types=1);

use App\Application\UseCases\CreateTaskUseCase;
use App\Application\UseCases\GetTaskStatusUseCase;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Infrastructure\Http\Handler\CreateTaskHandler;
use App\Infrastructure\Http\Handler\GetTaskStatusHandler;
use App\Infrastructure\Http\Request\Request;
use App\Infrastructure\Http\Router\Router;
use App\Infrastructure\Http\Response\JsonResponse;
use DI\Container;

require_once __DIR__ . '/../app/bootstrap.php';


$request = Request::fromGlobals();

if (!in_array($request->getMethod(), ['POST', 'GET'])) {
    (new JsonResponse(
        [
            'message' => 'Method not allowed',
        ],
        405,
    ))->send();
}

/**
 * @var TaskRepositoryInterface $repository
 * @var Container $container
 */
$repository = $container->get(
    TaskRepositoryInterface::class
);


$router = new Router();

$router->post(
    '/tasks',
    new CreateTaskHandler(new CreateTaskUseCase($repository)),
);

$statusUseCase = new GetTaskStatusUseCase($repository);

$router->get(
    '/tasks/{number}',
    new GetTaskStatusHandler($statusUseCase),
);

$router->dispatch($request);
