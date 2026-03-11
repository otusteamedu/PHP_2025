<?php
declare(strict_types=1);

namespace App\Config;

use App\Http\Api\v1\Task\CreateTask\CreateTaskController;
use App\Http\Api\v1\Task\GetTaskStatus\GetTaskStatusController;
use Slim\App;

class Routes
{
    public static function register(
        App $app,
        CreateTaskController $createTaskController,
        GetTaskStatusController $getTaskStatusController,
    ): void
    {
        $app->post('/api/v1/tasks', $createTaskController);
        $app->get('/api/v1/tasks/{request_id}', $getTaskStatusController);
    }
}
