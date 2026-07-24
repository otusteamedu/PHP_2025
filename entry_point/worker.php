<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Application\Handler\TaskConsoleHandler;
use App\Application\UseCases\ProcessTaskUseCase;
use App\Infrastructure\Database\Config\DatabaseConfigLoader;
use App\Infrastructure\Database\Connection\ConnectionFactory;
use App\Infrastructure\Database\Repository\TaskRepository;
use App\Infrastructure\Database\TaskDataMapper;
use App\Infrastructure\Factory\RabbitMqTaskQueueFactory;

$queue = (new RabbitMqTaskQueueFactory())->create();

echo "Worker started..." . PHP_EOL;

$config = DatabaseConfigLoader::load();

$pdo = ConnectionFactory::create($config);

$dataMapper = new TaskDataMapper($pdo);
$repository = new TaskRepository($dataMapper);

$useCase = new ProcessTaskUseCase($repository);

$queue->consume(
    new TaskConsoleHandler($useCase)
);