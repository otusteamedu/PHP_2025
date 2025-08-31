<?php

declare(strict_types=1);

namespace App\Infrastructure\Router;

use App\Presentation\Controller\StatementRequestController;
use App\Presentation\Controller\TelegramController;
use App\Presentation\Controller\TelegramWebhookController;
use App\Infrastructure\Logging\Logger;

final class Router
{
    public function __construct(
        private StatementRequestController $statementController,
        private TelegramController $telegramController,
        private TelegramWebhookController $webhookController
    ) {
    }

    /**
     * Обрабатывает входящий HTTP запрос и направляет к соответствующему контроллеру
     */
    public function handle(string $uri, string $method): void
    {
        $route = $this->determineRoute($uri, $method);

        match ($route) {
            'home' => $this->statementController->showForm(),
            'telegram_form' => $this->handleTelegramForm(),
            'create_statement' => $this->statementController->create(),
            'get_chat_id' => $this->telegramController->getChatId(),
            'webhook' => $this->webhookController->handleWebhook(),
            'set_webhook' => $this->webhookController->setWebhook(),
            default => $this->handleNotFound($uri, $method)
        };
    }

    /**
     * Определяет маршрут на основе URI и метода запроса
     */
    private function determineRoute(string $uri, string $method): string
    {
        if ($uri === '/' || $uri === '/form') {
            return 'home';
        }

        if ($this->isTelegramForm($uri)) {
            return 'telegram_form';
        }

        if ($uri === '/api/statement-request' && $method === 'POST') {
            return 'create_statement';
        }

        if ($uri === '/api/telegram/get-chat-id' && $method === 'POST') {
            return 'get_chat_id';
        }

        if ($uri === '/api/telegram/webhook' && $method === 'POST') {
            return 'webhook';
        }

        if ($uri === '/api/telegram/set-webhook' && $method === 'POST') {
            return 'set_webhook';
        }

        return 'not_found';
    }

    /**
     * Проверяет, является ли URI Telegram формой
     */
    private function isTelegramForm(string $uri): bool
    {
        return $uri === '/telegram' ||
               $uri === '/form-telegram' ||
               str_starts_with($uri, '/telegram?');
    }

    /**
     * Отображает Telegram Mini App форму
     */
    private function handleTelegramForm(): void
    {
        include __DIR__ . '/../../Presentation/View/statement-form-telegram.php';
    }

    /**
     * Обрабатывает 404 ошибку и возвращает JSON ответ
     */
    private function handleNotFound(string $uri, string $method): void
    {
        $logger = Logger::getInstance();
        $logger->warning('Route not found', ['uri' => $uri, 'method' => $method]);

        http_response_code(404);
        echo json_encode([
            'error' => 'Not found',
            'uri' => $uri,
            'method' => $method
        ], JSON_THROW_ON_ERROR);
    }
}
