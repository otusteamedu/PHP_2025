<?php

declare(strict_types=1);

namespace MkdBot\Presentation\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

class TelegramWebhookAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ?string $secretToken,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->secretToken === null || $this->secretToken === '') {
            return $handler->handle($request);
        }

        $token = $request->getHeaderLine('X-Telegram-Bot-Api-Secret-Token');

        if (!hash_equals($this->secretToken, $token)) {
            $this->logger->warning("Неверный секрет Telegram webhook");
            return (new Response())->withStatus(403);
        }

        return $handler->handle($request);
    }
}
