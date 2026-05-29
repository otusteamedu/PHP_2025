<?php

declare(strict_types=1);

namespace MkdBot\Presentation\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

/**
 * Middleware проверки webhook-секретов
 * Max: проверка заголовка X-Max-Bot-Api-Secret
 * Telegram: проверка заголовка X-Telegram-Bot-Api-Secret-Token (если установлен при setWebhook)
 */
class WebhookAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $maxWebhookSecret,
        private readonly ?string $telegramSecretToken,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        // Проверка секрета Max webhook
        if ($path === '/webhook/max' || str_starts_with($path, '/webhook/max/')) {
            $secret = $request->getHeaderLine('X-Max-Bot-Api-Secret');
            if ($this->maxWebhookSecret !== '' && $secret !== $this->maxWebhookSecret) {
                $this->logger->warning("Неверный секрет Max webhook: path={$path}");
                return (new Response())->withStatus(403);
            }
        }

        // Проверка секрета Telegram webhook (только если секрет был установлен)
        if (($path === '/webhook/telegram' || str_starts_with($path, '/webhook/telegram/')) && $this->telegramSecretToken !== null && $this->telegramSecretToken !== '') {
            $token = $request->getHeaderLine('X-Telegram-Bot-Api-Secret-Token');
            if ($token !== $this->telegramSecretToken) {
                $this->logger->warning("Неверный секрет Telegram webhook: path={$path}");
                return (new Response())->withStatus(403);
            }
        }

        return $handler->handle($request);
    }
}
