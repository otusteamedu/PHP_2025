<?php
declare(strict_types=1);

namespace App\Config;

use App\Application\UseCase\Task\CreateTaskHandler;
use App\Application\UseCase\Task\GetTaskStatusHandler;
use App\Application\UseCase\Task\ProcessTaskHandler;
use App\Http\Api\v1\Task\CreateTask\CreateTaskController;
use App\Http\Api\v1\Task\CreateTask\CreateTaskManager;
use App\Http\Api\v1\Task\GetTaskStatus\GetTaskStatusController;
use App\Http\Api\v1\Task\GetTaskStatus\GetTaskStatusManager;
use App\Infrastructure\Database\PdoFactory;
use App\Infrastructure\Http\JsonErrorHandler;
use App\Infrastructure\RabbitMq\RabbitMqClient;
use App\Infrastructure\RabbitMq\Task\TaskConsumer;
use App\Infrastructure\RabbitMq\Task\TaskPublisher;
use App\Infrastructure\Repository\PostgresTaskRepository;
use Slim\App;
use Slim\Factory\AppFactory;

class Application
{
    public static function build(): App
    {
        $app = AppFactory::create();

        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        $errorMiddleware = $app->addErrorMiddleware(true, true, true);
        $errorMiddleware->setDefaultErrorHandler(
            new JsonErrorHandler($app->getCallableResolver(), $app->getResponseFactory())
        );

        $pdo = PdoFactory::create();
        $taskRepository = new PostgresTaskRepository($pdo);
        $taskPublisher = new TaskPublisher(new RabbitMqClient());

        $createTaskController = new CreateTaskController(
            new CreateTaskManager(new CreateTaskHandler($taskRepository, $taskPublisher))
        );

        $getTaskStatusController = new GetTaskStatusController(
            new GetTaskStatusManager(new GetTaskStatusHandler($taskRepository))
        );

        Routes::register($app, $createTaskController, $getTaskStatusController);

        return $app;
    }

    public static function buildTaskWorker(): TaskConsumer
    {
        $taskRepository = new PostgresTaskRepository(PdoFactory::create());

        return new TaskConsumer(new RabbitMqClient(), new ProcessTaskHandler($taskRepository));
    }
}
