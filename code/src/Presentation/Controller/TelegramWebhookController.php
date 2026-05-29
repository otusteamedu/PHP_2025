<?php

declare(strict_types=1);

namespace MkdBot\Presentation\Controller;

use MkdBot\Application\UseCase\HandleTelegramWebhook;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Контроллер webhook Telegram — endpoint /webhook/telegram
 * v1: минимальный контроллер — логирует получение webhook, возвращает 200 OK
 */
class TelegramWebhookController
{
    public function __construct(
        private readonly HandleTelegramWebhook $handleTelegramWebhook,
    ) {
    }

    /**
     * Обрабатывает входящий webhook от Telegram API
     */
    public function handle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getBody()->getContents();

        $this->handleTelegramWebhook->execute($body);

        return $response->withStatus(200);
    }
}
