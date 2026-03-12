<?php

declare(strict_types=1);

namespace App\UserInterface\Api;

use App\Application\Services\SetRequest\Query as SetRequestQuery;
use App\Application\Services\SetRequest\RequestHandler as SetRequestHandler;
use App\Application\Services\GetRequest\Query as GetRequestQuery;
use App\Application\Services\GetRequest\RequestHandler as GetRequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

readonly class Controller
{
    public function __construct(
        private SetRequestHandler $setRequestHandler,
        private GetRequestHandler $getRequestHandler,
    )
    {
    }

    public function request(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array) $request->getParsedBody();
        $data = $params['request_data'] ?? null;

        if (!is_string($data) || trim($data) === '') {
            throw new HttpBadRequestException($request, 'Данные запроса не указаны');
        }

        $output = $this->setRequestHandler->handle(new SetRequestQuery($data));

        $response->getBody()->write((string) json_encode($output, JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(202);
    }

    public function information (ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'];

        $output = $this->getRequestHandler->handle(new GetRequestQuery((int)$id));

        if(!$output){
            throw new HttpBadRequestException($request, 'Запись не найдена');
        }

        $response->getBody()->write((string) json_encode($output, JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
