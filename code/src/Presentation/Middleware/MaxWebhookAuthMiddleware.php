<?php

declare(strict_types=1);

namespace MkdBot\Presentation\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

class MaxWebhookAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $webhookSecret,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $secret = $request->getHeaderLine('X-Max-Bot-Api-Secret');

        if ($this->webhookSecret !== '' && !hash_equals($this->webhookSecret, $secret)) {
            $this->logger->warning("Неверный секрет Max webhook");
            return (new Response())->withStatus(403);
        }

        return $handler->handle($request);
    }
}
