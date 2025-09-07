<?php

namespace App\Infrastructure\Controllers;

use App\Helpers\HttpHelper;
use App\Application\Services\TaskService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TaskController 
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function check(Request $request, Response $response, $args) 
    {
        $taskId = (int) $args['id'];
        $userId = (int) $request->getAttribute('userid');

        $status = $this->taskService->getStatusById($taskId, $userId);

        if (empty($status)) {
            return HttpHelper::send404Error($response);
        }
        return HttpHelper::sendData(['task_id' => $taskId, 'status' => $status], $response);
    }
}