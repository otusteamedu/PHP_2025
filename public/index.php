<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Factory\RepositoryFactory;
use App\Service\EventService;
use App\Controller\EventController;
use Dotenv\Dotenv;

// загружаем .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$config = [
    'storage' => $_ENV['STORAGE'],
    'redis' => [
        'scheme' => $_ENV['REDIS_SCHEME'],
        'host' => $_ENV['REDIS_HOST'],
        'port' => $_ENV['REDIS_PORT'],
    ],
    'elastic' => [
        'host' => $_ENV['ELASTIC_HOST'],
    ]
];

$repository = RepositoryFactory::create($config);
$service = new EventService($repository);
$controller = new EventController($service);
$controller->handleRequest();
