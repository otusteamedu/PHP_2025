<?php
use Slim\Factory\AppFactory;
use App\Controllers\HomeController;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->post('/api/add', [HomeController::class, 'add']);
$app->post('/api/answer', [HomeController::class, 'answer']);
$app->post('/api/clear', [HomeController::class, 'clear']);

$app->get('/api/events', [HomeController::class, 'events']);

$app->run();