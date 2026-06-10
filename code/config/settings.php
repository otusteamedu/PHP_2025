<?php

declare(strict_types=1);

// .env загружается в config/bootstrap.php (единая точка входа)
// Здесь только читаем $_ENV — без побочных эффектов

return [
    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'mkd-bot',
        'env' => $_ENV['APP_ENV'] ?? 'development',
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    ],

    'postgres' => [
        'host' => $_ENV['PG_HOST'] ?? 'postgres',
        'port' => (int)($_ENV['PG_PORT'] ?? 5432),
        'database' => $_ENV['PG_DB'] ?? 'mkd_bot',
        'user' => $_ENV['PG_USER'] ?? 'mkd_user',
        'password' => $_ENV['PG_PASSWORD'] ?? 'secret',
    ],

    'rabbitmq' => [
        'host' => $_ENV['RABBITMQ_HOST'] ?? 'rabbitmq',
        'port' => (int)($_ENV['RABBITMQ_PORT'] ?? 5672),
        'login' => $_ENV['RABBITMQ_LOGIN'] ?? 'guest',
        'password' => $_ENV['RABBITMQ_PASSWORD'] ?? 'guest',
        'vhost' => $_ENV['RABBITMQ_VHOST'] ?? '/',
        'queue_telegram_forward' => $_ENV['RABBITMQ_QUEUE_TELEGRAM_FORWARD'] ?? 'mkd.telegram.forward',
        'queue_rag_query' => $_ENV['RABBITMQ_QUEUE_RAG_QUERY'] ?? 'mkd.rag.query',
        'queue_fallback' => $_ENV['RABBITMQ_QUEUE_FALLBACK'] ?? 'mkd.fallback',
    ],

    'max' => [
        'token' => $_ENV['MAX_TOKEN'] ?? '',
        'channel' => $_ENV['MAX_CHANNEL'] ?? '',
        'webhook_secret' => $_ENV['MAX_WEBHOOK_SECRET'] ?? '',
    ],

    'telegram' => [
        'token' => $_ENV['TELEGRAM_TOKEN'] ?? '',
        'channel' => $_ENV['TELEGRAM_CHANNEL'] ?? '',
        'secret_token' => $_ENV['TELEGRAM_SECRET_TOKEN'] ?? null,
        'http_proxy' => $_ENV['TELEGRAM_HTTP_PROXY'] ?? '',
        'proxy_login' => $_ENV['TELEGRAM_PROXY_LOGIN'] ?? '',
        'proxy_password' => $_ENV['TELEGRAM_PROXY_PASSWORD'] ?? '',
        'mode' => $_ENV['TELEGRAM_MODE'] ?? 'longpoll',
    ],

    // SMTP для уведомлений о предложениях
    'smtp' => [
        'host' => $_ENV['SMTP_HOST'] ?? '',
        'port' => (int)($_ENV['SMTP_PORT'] ?? 465),
        'user' => $_ENV['SMTP_USER'] ?? '',
        'password' => $_ENV['SMTP_PASSWORD'] ?? '',
        'from_email' => $_ENV['SMTP_FROM_EMAIL'] ?? '',
        'from_name' => $_ENV['SMTP_FROM_NAME'] ?? 'МКД Бот',
    ],

    'proposal_notify_emails' => $_ENV['PROPOSAL_NOTIFY_EMAILS'] ?? '',

    // RAG-поиск через Yandex Cloud Function
    'rag' => [
        'search_url' => $_ENV['RAG_SEARCH_URL'] ?? '',
        'api_key' => $_ENV['RAG_SEARCH_API_KEY'] ?? '',
        'timeout' => (int)($_ENV['RAG_SEARCH_TIMEOUT'] ?? 35),
    ],

    'logger' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'DEBUG',
        'path' => __DIR__ . '/../logs/app.log',
    ],
];
