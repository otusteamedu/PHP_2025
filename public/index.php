<?php

use App\Infrastructure\Controllers\AuthController;
use App\Infrastructure\Controllers\TodoController;
use App\Infrastructure\Middlewares\JsonBodyParserMiddleware;
use App\Infrastructure\Middlewares\JwtMiddleware;
use App\Infrastructure\Repositories\TodoRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Application\Services\JwtService;
use App\Application\Services\TodoService;
use App\Application\Services\UserService;
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
$container->set(TodoRepository::class, function (ContainerInterface $container) {
    return new TodoRepository($container->get(PDO::class));
});
$container->set(JwtService::class, function (ContainerInterface $container) {
    return new JwtService();
});
$container->set(UserService::class, function (ContainerInterface $container) {
    return new UserService($container->get(UserRepository::class), $container->get(JwtService::class));
});
$container->set(TodoService::class, function (ContainerInterface $container) {
    return new TodoService($container->get(TodoRepository::class));
});

$app->add(JsonBodyParserMiddleware::class);

$app->post('/register', [AuthController::class, 'register']);
$app->post('/login', [AuthController::class, 'login']);

$app->get('/todos', [TodoController::class, 'index'])->add(JwtMiddleware::class);
$app->get('/todos/{id}', [TodoController::class, 'show'])->add(JwtMiddleware::class);
$app->post('/todos', [TodoController::class, 'store'])->add(JwtMiddleware::class);
$app->patch('/todos/{id}', [TodoController::class, 'update'])->add(JwtMiddleware::class);
$app->delete('/todos/{id}', [TodoController::class, 'delete'])->add(JwtMiddleware::class);

$app->run();
