<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Controllers\MongoController;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->post('/api/add', [MongoController::class, 'add']);
$app->post('/api/answer', [MongoController::class, 'answer']);
$app->post('/api/clear', [MongoController::class, 'clear']);

$app->get('/api/event-conditions', [MongoController::class, 'showEventConditions']);
$app->get('/api/event-priorities', [MongoController::class, 'showEventPriorities']);

$app->run();