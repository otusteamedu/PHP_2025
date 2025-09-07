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

$app->post('/register', [AuthController::class, 'register']);
$app->post('/login', [AuthController::class, 'login']);

$app->get('/todos', [TodoController::class, 'index'])->add(JwtMiddleware::class);
$app->get('/todos/{id}', [TodoController::class, 'show'])->add(JwtMiddleware::class);
$app->post('/todos', [TodoController::class, 'store'])->add(JwtMiddleware::class);
$app->patch('/todos/{id}', [TodoController::class, 'update'])->add(JwtMiddleware::class);
$app->delete('/todos/{id}', [TodoController::class, 'delete'])->add(JwtMiddleware::class);

$app->get('/check/{id}', [TaskController::class, 'check'])->add(JwtMiddleware::class);

$app->run();
