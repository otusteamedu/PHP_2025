<?php

namespace App\Infrastructure\Controllers;

use App\Helpers\HttpHelper;
use App\Application\Services\TodoService;
use App\Application\Services\TaskService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TodoController 
{
    public function __construct(
        private TodoService $todoService,
        private TaskService $taskService
    ) {}

    public function index(Request $request, Response $response, $args) 
    {
        $userId = (int) $request->getAttribute('userid');
        $todos = $this->todoService->getByUserId($userId);
        return HttpHelper::sendData($todos, $response);
    }

    public function show(Request $request, Response $response, $args) 
    {
        $todoId = (int) $args['id'];
        $userId = (int) $request->getAttribute('userid');

        $todo = $this->todoService->getById($todoId, $userId);

        if (empty($todo)) {
            return HttpHelper::send404Error($response);
        }
        return HttpHelper::sendData($todo, $response);
    }

    public function store(Request $request, Response $response, $args) 
    {
        $parsedBody = $request->getParsedBody();
        $userId = (int) $request->getAttribute('userid');

        $content = htmlspecialchars($parsedBody['content']);
        if (strlen($content) < 3) {
            return HttpHelper::send400Error('Текст должен содержать не менее 3 символов', $response);
        }

        $data = ['type' => 'add', 'content' => $content, 'user_id' => $userId];
        $taskId = $this->taskService->sendMessage($data);

        return HttpHelper::sendData(['message' => 'Задание добавлено в очередь', 'task_id' => $taskId], $response);
    }

    public function update(Request $request, Response $response, $args)
    {
        $parsedBody = $request->getParsedBody();
        $userId = (int) $request->getAttribute('userid'); 
        $todoId = (int) $args['id'];
        
        $content = htmlspecialchars($parsedBody['content']);
        if (strlen($content) < 3) {
            return HttpHelper::send400Error('Текст должен содержать не менее 3 символов', $response);
        }

        $data = ['type' => 'update', 'content' => $content, 'user_id' => $userId, 'todo_id' => $todoId];
        $taskId = $this->taskService->sendMessage($data);

        return HttpHelper::sendData(['message' => 'Задание добавлено в очередь', 'task_id' => $taskId], $response);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $userId = (int) $request->getAttribute('userid'); 
        $todoId = (int) $args['id'];

        $data = ['type' => 'delete', 'user_id' => $userId, 'todo_id' => $todoId];
        $taskId = $this->taskService->sendMessage($data);

        return HttpHelper::sendData(['message' => 'Задание добавлено в очередь', 'task_id' => $taskId], $response);
    }
}