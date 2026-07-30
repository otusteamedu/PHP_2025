<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Application\Handler\TaskConsoleHandler;
use App\Application\UseCases\ProcessTaskUseCase;
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


$queue = (new RabbitMqTaskQueueFactory())->create($repository);

echo "Worker started..." . PHP_EOL;


$useCase = new ProcessTaskUseCase($repository);


try {
    $queue->consume(
        new TaskConsoleHandler($useCase)
    );
} finally {
    $queue->close();
}
