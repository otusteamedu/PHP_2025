<?php
use App\Application\Services\TaskService;
use App\Infrastructure\Queue\RabbitmqReceiver;
use App\Infrastructure\Queue\RabbitmqSender;
use App\Infrastructure\Repositories\TaskRepository;
use App\Infrastructure\Repositories\TodoRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Application\Services\JwtService;
use App\Application\Services\TodoService;
use App\Application\Services\UserService;
use DI\Container;
use Psr\Container\ContainerInterface;

$container = new Container ();

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
$container->set(TaskRepository::class, function (ContainerInterface $container) {
    return new TaskRepository($container->get(PDO::class));
});

$container->set(RabbitmqSender::class, function (ContainerInterface $container) {
    return new RabbitmqSender();
});
$container->set(RabbitmqReceiver::class, function (ContainerInterface $container) {
    return new RabbitmqReceiver($container->get(TodoService::class), $container->get(TaskService::class));
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
$container->set(TaskService::class, function (ContainerInterface $container) {
    return new TaskService($container->get(TaskRepository::class), $container->get(RabbitmqSender::class));
});

return $container;