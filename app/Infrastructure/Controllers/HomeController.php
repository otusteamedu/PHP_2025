<?php
namespace App\Infrastructure\Controllers;

use App\Application\UseCases\Commands\AddEvent\Dto;
use App\Application\UseCases\Commands\AddEvent\Handler;
use App\Application\UseCases\Commands\ClearEvents\Handler as ClearHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Application\UseCases\Queries\FetchEvent\Fetcher;
use App\Application\UseCases\Queries\FetchAll\Fetcher as AllFetcher;
use App\Application\UseCases\Queries\FetchEvent\Dto as FetchDto;

class HomeController
{
    public function __construct(
        private Handler $handler,
        private Fetcher $fetcher,
        private AllFetcher $allFetcher,
        private ClearHandler $clearHandler
    ) {}

    public function add(Request $request, Response $response, $args): Response
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        $event = (string) $data['event'] ?? '';
        $priority = (int) $data['priority'] ?? 0;
        $conditions = (array) $data['conditions'] ?? [];
        ksort($conditions);

        if (empty($event) || empty($priority) || empty($conditions)) {
            $resp = json_encode(['message' => 'Ошибка: невалидное описание события']);
            $response->getBody()->write($resp);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $dto = new Dto($event, $priority, $conditions);
        $this->handler->handle($dto);
        
        $resp = json_encode(['message' => 'Событие добавлено в систему']);
        $response->getBody()->write($resp);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function answer(Request $request, Response $response, $args): Response
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);
        $parameters = $data['params'] ?? [];

        if (empty($parameters)) {
            $resp = json_encode(['message' => 'Некорректный запрос']);
            $response->getBody()->write($resp);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        ksort($parameters);

        $dto = new FetchDto($parameters);
        $answer = $this->fetcher->fetch($dto);

        $resp = json_encode(['message' => $answer]);
        $response->getBody()->write($resp);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function events(Request $request, Response $response, $args): Response
    {
        $message = $this->allFetcher->fetch();

        $resp = json_encode(['message' => $message]);
        $response->getBody()->write($resp);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function clear(Request $request, Response $response, $args): Response
    {
        $this->clearHandler->handle();

        $resp = json_encode(['message' => 'Хранилище событий очищено']);
        $response->getBody()->write($resp);
        return $response->withHeader('Content-Type', 'application/json');
    }
}