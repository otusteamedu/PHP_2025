<?php

require_once __DIR__ . '/vendor/autoload.php';

use Arlex2305k\Redis\Storage\StorageFactory;
use Arlex2305k\Redis\EventService;
use Arlex2305k\Redis\RequestHandler;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$storageType = $_ENV['STORAGE_TYPE'] ?? 'redis';

try {
    $storage = StorageFactory::createStorage($storageType);
    $eventService = new EventService($storage);
    $requestHandler = new RequestHandler($eventService);

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $requestHandler->handleRequest($method, $uri);
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
    exit;
}
