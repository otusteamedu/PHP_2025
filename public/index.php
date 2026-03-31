<?php

use App\Controller\TodoController;
use App\Repository\InMemoryTodoRepository;
use App\Service\TodoService;

require_once __DIR__ . '/../vendor/autoload.php';

$repo = new InMemoryTodoRepository();
$service = new TodoService($repo);
$controller = new TodoController($service);

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

if ($method === 'GET' && $path === '/todos') {
    $controller->index();
} elseif ($method === 'POST' && $path === '/todos') {
    $controller->create();
} elseif ($method === 'DELETE' && preg_match('#^/todos/(\d+)$#', $path, $matches)) {
    $controller->delete((int)$matches[1]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found']);
}
