<?php

declare(strict_types=1);

namespace App\UserInterface\Api;

use App\Application\Services\SetRequest\Query;
use App\Application\Services\SetRequest\RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

readonly class Controller
{
    public function __construct(private RequestHandler $handler)
    {
    }

    public function request(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array) $request->getParsedBody();
        $data = $params['request_data'] ?? null;

        if (!is_string($data) || trim($data) === '') {
            throw new HttpBadRequestException($request, 'Request data is not found');
        }

        $output = $this->handler->handle(new Query($data));

        $response->getBody()->write((string) json_encode($output, JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(202);
    }
}
