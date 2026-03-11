<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface as Response;

class JsonResponder
{
    public static function respond(Response $response, array|JsonSerializable $data, int $status = 200): Response
    {
        $response->getBody()->write((string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
