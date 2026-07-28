<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Application\UseCases\AddTaskToQueueUseCase;
use App\Application\UseCases\GetTenNewTasksUseCase;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Infrastructure\Factory\RabbitMqTaskQueueFactory;
use DI\Container;

/**
 * @var TaskRepositoryInterface $repository
 * @var Container $container
 */
$repository = $container->get(
   TaskRepositoryInterface::class
);

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
