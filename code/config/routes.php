<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use MkdBot\Presentation\Middleware\WebhookAuthMiddleware;

return function (App $app): void {
    // Webhook-и — с проверкой секретов
    $app->group('/webhook', function (RouteCollectorProxy $group) {
        $group->post('/max', \MkdBot\Presentation\Controller\MaxWebhookController::class . ':handle');
        $group->post('/telegram', \MkdBot\Presentation\Controller\TelegramWebhookController::class . ':handle');
    })->add(WebhookAuthMiddleware::class);

    // Health-check — без middleware
    $app->get('/health', \MkdBot\Presentation\Controller\HealthController::class . ':health');
    $app->get('/ready', \MkdBot\Presentation\Controller\HealthController::class . ':ready');
};
