<?php

declare(strict_types=1);

namespace Api\Presentation\Api\Middleware;

use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestLoggingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Logger $logger
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';

        $this->logger->info('HTTP запрос', [
            'method' => $method,
            'uri' => (string) $uri,
            'ip' => $ip,
        ]);

        $response = $handler->handle($request);

        $this->logger->info('HTTP ответ', [
            'method' => $method,
            'uri' => (string) $uri,
            'status' => $response->getStatusCode(),
        ]);

        return $response;
    }
}
