<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Controllers\HomeController;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->post('/api/add', [HomeController::class, 'add']);
$app->post('/api/answer', [HomeController::class, 'answer']);
$app->post('/api/clear', [HomeController::class, 'clear']);

$app->get('/api/event-conditions', [HomeController::class, 'showEventConditions']);
$app->get('/api/event-priorities', [HomeController::class, 'showEventPriorities']);

$app->run();