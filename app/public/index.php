<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\JobController;
use App\Repositories\JobRepository;
use App\Services\RedisQueueService;

// Загрузка environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Инициализация БД
$db = new PDO(
    "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']}",
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD']
);

// Инициализация зависимостей
$jobRepository = new JobRepository($db);
$queueService = new RedisQueueService();
$controller = new JobController($jobRepository, $queueService);

// Маршрутизация
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST' && $requestUri === '/api/jobs') {
    echo $controller->create((object) ['getContent' => fn() => file_get_contents('php://input')]);
} elseif ($requestMethod === 'GET' && preg_match('#^/api/jobs/([^/]+)$#', $requestUri, $matches)) {
    echo $controller->status($matches[1]);
} elseif ($requestMethod === 'GET' && $requestUri === '/api/docs.json') {
    header('Content-Type: application/json');
    readfile(__DIR__ . '/../docs/swagger.json');
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}