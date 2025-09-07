<?php
use App\Infrastructure\Controllers\AuthController;
use App\Infrastructure\Controllers\TaskController;
use App\Infrastructure\Controllers\TodoController;
use App\Infrastructure\Middlewares\JsonBodyParserMiddleware;
use App\Infrastructure\Middlewares\JwtMiddleware;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../bootstrap/dependencies.php';
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->add(JsonBodyParserMiddleware::class);

$app->post('/api/v1/register', [AuthController::class, 'register']);
$app->post('/api/v1/login', [AuthController::class, 'login']);

$app->get('/api/v1/todos', [TodoController::class, 'index'])->add(JwtMiddleware::class);
$app->get('/api/v1/todos/{id}', [TodoController::class, 'show'])->add(JwtMiddleware::class);
$app->post('/api/v1/todos', [TodoController::class, 'store'])->add(JwtMiddleware::class);
$app->patch('/api/v1/todos/{id}', [TodoController::class, 'update'])->add(JwtMiddleware::class);
$app->delete('/api/v1/todos/{id}', [TodoController::class, 'delete'])->add(JwtMiddleware::class);

$app->get('/api/v1/check/{id}', [TaskController::class, 'check'])->add(JwtMiddleware::class);

$app->run();
