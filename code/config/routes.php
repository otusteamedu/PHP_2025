<?php

declare(strict_types=1);

use Slim\App;
use MkdBot\Presentation\Controller\HealthController;
use MkdBot\Presentation\Controller\MaxWebhookController;
use MkdBot\Presentation\Controller\TelegramWebhookController;
use MkdBot\Presentation\Middleware\MaxWebhookAuthMiddleware;
use MkdBot\Presentation\Middleware\TelegramWebhookAuthMiddleware;

return function (App $app): void {
    // Max webhook — с проверкой Max-секрета
    $app->post('/webhook/max', [MaxWebhookController::class, 'handle'])
        ->add(MaxWebhookAuthMiddleware::class);

    // Telegram webhook — с проверкой Telegram-секрета
    $app->post('/webhook/telegram', [TelegramWebhookController::class, 'handle'])
        ->add(TelegramWebhookAuthMiddleware::class);

    // Health-check — без middleware
    $app->get('/health', [HealthController::class, 'health']);
    $app->get('/ready', [HealthController::class, 'ready']);
};
