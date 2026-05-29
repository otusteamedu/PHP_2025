<?php

declare(strict_types=1);

/**
 * Скрипт регистрации webhook URL в Max API и Telegram API
 * Запуск: php bin/register-webhooks.php
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bootstrap.php';

use DI\ContainerBuilder;
use MkdBot\Infrastructure\Interface\MaxWebhookRegistrarInterface;
use MkdBot\Infrastructure\Interface\TelegramWebhookClientInterface;

try {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->addDefinitions(require __DIR__ . '/../config/dependencies.php');
    $container = $containerBuilder->build();

    $settings = $container->get('settings');
    $appDomain = $_ENV['APP_DOMAIN'];
    $webhookBaseUrl = "https://{$appDomain}";

    echo "Регистрация Max webhook...\n";
    $maxRegistrar = $container->get(MaxWebhookRegistrarInterface::class);
    $maxRegistrar->subscribe(
        url: "{$webhookBaseUrl}/webhook/max",
        secret: $settings['max']['webhook_secret'],
        updateTypes: ['message_created', 'message_callback', 'bot_started', 'bot_stopped'],
    );
    echo "+++ Max webhook зарегистрирован: {$webhookBaseUrl}/webhook/max\n";

    echo "Регистрация Telegram webhook...\n";
    $telegramClient = $container->get(TelegramWebhookClientInterface::class);
    $telegramParams = [
        'url' => "{$webhookBaseUrl}/webhook/telegram",
        'allowed_updates' => json_encode(['message', 'channel_post']),
    ];

    $secretToken = $settings['telegram']['secret_token'] ?? null;
    if ($secretToken !== null && $secretToken !== '') {
        $telegramParams['secret_token'] = $secretToken;
    }

    $telegramClient->setWebhook($telegramParams);
    echo "+++ Telegram webhook зарегистрирован: {$webhookBaseUrl}/webhook/telegram\n";

    echo "\n+++ Все webhook-и зарегистрированы!\n";
} catch (\Throwable $e) {
    echo "--- Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}
