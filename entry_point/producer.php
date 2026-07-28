<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Application\Handler\TaskConsoleHandler;
use App\Application\UseCases\AddTaskToQueueUseCase;
use App\Application\UseCases\GetTenNewTasksUseCase;
use App\Infrastructure\Database\Config\DatabaseConfigLoader;
use App\Infrastructure\Database\Connection\ConnectionFactory;
use App\Infrastructure\Database\Repository\TaskRepository;
use App\Infrastructure\Database\TaskDataMapper;
use App\Infrastructure\Factory\RabbitMqTaskQueueFactory;

$config = DatabaseConfigLoader::load();

$pdo = ConnectionFactory::create($config);

$dataMapper = new TaskDataMapper($pdo);
$repository = new TaskRepository($dataMapper);
$queue = (new RabbitMqTaskQueueFactory())->create();


echo "Producer started..." . PHP_EOL;
$addTaskToQueueUseCase = new AddTaskToQueueUseCase($repository, $queue);

$getTasksUseCase = new GetTenNewTasksUseCase($repository);

while (1) {
    $tasks = $getTasksUseCase->execute();

    foreach ($tasks as $task) {
        $addTaskToQueueUseCase->execute($task);
    }

    sleep(10);
}
