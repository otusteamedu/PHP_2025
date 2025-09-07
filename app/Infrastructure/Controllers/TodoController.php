<?php

namespace App\Infrastructure\Controllers;

use App\Helpers\HttpHelper;
use App\Application\Services\TodoService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TodoController 
{
    public function __construct(private TodoService $service) {}

    public function index(Request $request, Response $response, $args) 
    {
        $userId = (int) $request->getAttribute('userid');
        $todos = $this->service->getByUserId($userId);
        return HttpHelper::sendData($todos, $response);
    }

    public function show(Request $request, Response $response, $args) 
    {
        $todoId = (int) $args['id'];
        $userId = (int) $request->getAttribute('userid');

        $todo = $this->service->getById($todoId, $userId);

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

        $this->service->add($content, $userId);

        return HttpHelper::sendData(['message' => 'OK'], $response);
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

        $this->service->update($content, $todoId, $userId);

        return HttpHelper::sendData(['message' => 'OK'], $response);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $userId = (int) $request->getAttribute('userid'); 
        $todoId = (int) $args['id'];

        $this->service->delete($todoId, $userId);

        return HttpHelper::sendData(['message' => 'OK'], $response);
    }
}