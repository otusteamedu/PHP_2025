<?php

declare(strict_types=1);

namespace Api\Presentation\Api\Helpers;

use Psr\Http\Message\ResponseInterface as Response;

final class Json
{
    public static function response(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()
            ->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
