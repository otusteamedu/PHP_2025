<?php

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/services.php';

use App\Controller\StatementController;

// Маршрутизация
$requestUri = $_SERVER['REQUEST_URI'];

if ($requestUri === '/' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    include __DIR__ . '/../templates/form.php';
    exit;
}

if ($requestUri === '/generate-statement' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $queueService = $config[App\Service\QueueServiceInterface::class]();
    $controller = new StatementController($queueService);
    $controller->handleRequest();
    exit;
}

http_response_code(404);
echo "Page not found";