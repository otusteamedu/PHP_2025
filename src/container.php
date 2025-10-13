<?php
use Psr\Container\ContainerInterface;
use App\Infrastructure\RabbitConnection;
use App\Infrastructure\Database;
use App\Repository\TaskRepository;
use App\Controller\TaskController;

return [
    RabbitConnection::class => function(ContainerInterface $c) {
        $host = $_ENV['RABBITMQ_HOST'];
        $port = (int)$_ENV['RABBITMQ_PORT'];
        $user = $_ENV['RABBITMQ_USER'] ?? 'guest';
        $pass = $_ENV['RABBITMQ_PASS'] ?? 'guest';
        $vhost = $_ENV['RABBITMQ_VHOST'] ?? '/';
        $queue = $_ENV['RABBITMQ_QUEUE'] ?? 'tasks';
        return new RabbitConnection($host, $port, $user, $pass, $vhost, $queue);
    },
    Database::class => function(ContainerInterface $c) {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $database = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        return new Database($host, $port, $database, $user, $pass);
    },
    TaskRepository::class => function(ContainerInterface $c) {
        return new TaskRepository($c->get(Database::class));
    },
    TaskController::class => function(ContainerInterface $c) {
        return new TaskController(
            $c->get(RabbitConnection::class),
            $c->get(TaskRepository::class)
        );
    },
];
