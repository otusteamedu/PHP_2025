<?php

use App\Controllers\AuthController;
use App\Controllers\TodoController;
use App\Middlewares\JsonBodyParserMiddleware;
use App\Repositories\UserRepository;
use App\Services\UserService;
use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container ();
AppFactory::setContainer($container);
$app = AppFactory::create();

$container->set(PDO::class, function () {
    $host = getenv('MYSQL_HOST');
    $dbname = getenv('MYSQL_DBNAME');
    $username = getenv('MYSQL_USERNAME');
    $password = getenv('MYSQL_PASSWORD');
        
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ]);
        
    return $pdo;
});
$container->set(UserRepository::class, function (ContainerInterface $container) {
    return new UserRepository($container->get(PDO::class));
});
$container->set(UserService::class, function (ContainerInterface $container) {
    return new UserService($container->get(UserRepository::class));
});

$app->add(JsonBodyParserMiddleware::class);

$app->get('/todos', [TodoController::class, 'index']);
$app->post('/register', [AuthController::class, 'register']);

$app->run();
