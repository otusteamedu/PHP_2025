<?php

declare(strict_types=1);

use App\Application\UseCases\CreateTaskUseCase;
use App\Infrastructure\Database\Config\DatabaseConfigLoader;
use App\Infrastructure\Database\Connection\ConnectionFactory;
use App\Infrastructure\Database\Repository\TaskRepository;
use App\Infrastructure\Database\TaskDataMapper;
use App\Infrastructure\Http\Handler\CreateTaskHandler;
use App\Infrastructure\Http\Request\Request;
use App\Infrastructure\Http\Router\Router;
use App\Infrastructure\Http\Response\JsonResponse;


require_once __DIR__ . '/../app/bootstrap.php';


$request = Request::fromGlobals();

if ($request->getMethod() !== 'POST') {
    (new JsonResponse(
        [
            'message' => 'Method not allowed',
        ],
        405,
    ))->send();
}


$config = DatabaseConfigLoader::load();

$pdo = ConnectionFactory::create($config);

$dataMapper = new TaskDataMapper($pdo);
$repository = new TaskRepository($dataMapper);



$router = new Router();


$router->post(
    '/tasks',
    new CreateTaskHandler(new CreateTaskUseCase($repository)),
);


$router->dispatch($request);

