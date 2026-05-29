<?php

declare(strict_types=1);

namespace MkdBot\Tests\E2E;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;

/**
 * Базовый класс для E2E-тестов
 *
 * Предоставляет общий HTTP-клиент, базовый URL сервера
 * и чтение секретов из .env для реальных webhook-запросов.
 *
 * Базовый URL определяется по приоритету:
 * 1. E2E_BASE_URL — явное переопределение (escape-hatch, высший приоритет)
 * 2. APP_DOMAIN + E2E_USE_INTERNAL_DNS — вычисление из домена и флага DNS
 *    - E2E_USE_INTERNAL_DNS=true  -> http://webserver (внутри Docker-сети)
 *    - E2E_USE_INTERNAL_DNS=false -> https://<APP_DOMAIN> (внешний URL)
 */
abstract class BaseE2ETest extends TestCase
{
    /**
     * Базовый URL сервера:
     * - E2E_BASE_URL (явное переопределение, высший приоритет)
     * - либо вычисляется из APP_DOMAIN + E2E_USE_INTERNAL_DNS
     */
    protected function getBaseUrl(): string
    {
        // 1. Явное переопределение (высший приоритет — escape-hatch)
        $explicit = $this->getEnvVar('E2E_BASE_URL', '');
        if ($explicit !== '') {
            return $explicit;
        }

        // 2. Вычисление из APP_DOMAIN + E2E_USE_INTERNAL_DNS
        $domain = $this->getEnvVar('APP_DOMAIN', 'webserver');
        $useInternalDns = filter_var(
            $this->getEnvVar('E2E_USE_INTERNAL_DNS', 'true'),
            FILTER_VALIDATE_BOOLEAN,
        );

        if ($useInternalDns) {
            return 'http://webserver';
        }

        return 'https://' . $domain;
    }

    /**
     * HTTP-клиент с настройками: timeout 10s, без проверки SSL
     */
    protected function httpClient(): Client
    {
        return new Client([
            'base_uri' => $this->getBaseUrl(),
            'timeout' => 10,
            'verify' => false,
            'http_errors' => false, // не бросать исключения на 4xx/5xx
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Секрет Max webhook из .env (заголовок X-Max-Bot-Api-Secret)
     */
    protected function getMaxWebhookSecret(): string
    {
        return $this->getEnvVar('MAX_WEBHOOK_SECRET', '');
    }

    /**
     * Секрет Telegram webhook из .env (заголовок X-Telegram-Bot-Api-Secret-Token)
     */
    protected function getTelegramSecretToken(): string
    {
        return $this->getEnvVar('TELEGRAM_SECRET_TOKEN', '');
    }

    /**
     * Получает переменную окружения: сначала из getenv(), затем из $_ENV,
     * затем парсит code/.env файл напрямую
     */
    protected function getEnvVar(string $name, string $default = ''): string
    {
        // Пытаемся получить из getenv()
        $value = getenv($name);
        if ($value !== false && $value !== '') {
            return $value;
        }

        // Пытаемся получить из $_ENV
        if (isset($_ENV[$name]) && $_ENV[$name] !== '') {
            return $_ENV[$name];
        }

        // Парсим .env файл напрямую
        $envValue = $this->parseEnvFile($name);
        if ($envValue !== null && $envValue !== '') {
            return $envValue;
        }

        return $default;
    }

    /**
     * Подгружает переменные из code/.env в окружение, если ещё не загружены
     */
    private function loadEnvIfNeeded(): void
    {
        static $loaded = false;

        if ($loaded) {
            return;
        }

        $envPath = dirname(__DIR__, 2) . '/.env';
        if (!file_exists($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Пропускаем комментарии
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Парсим KEY=VALUE
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Устанавливаем только если ещё нет в окружении
                if (getenv($key) === false) {
                    putenv("{$key}={$value}");
                    $_ENV[$key] = $value;
                }
            }
        }

        $loaded = true;
    }

    /**
     * Читает конкретную переменную из code/.env файла
     */
    private function parseEnvFile(string $searchKey): ?string
    {
        $envPath = dirname(__DIR__, 2) . '/.env';
        if (!file_exists($envPath)) {
            return null;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                if (trim($key) === $searchKey) {
                    return trim($value);
                }
            }
        }

        return null;
    }

    protected function setUp(): void
    {
        // Подгружаем .env для получения секретов
        $this->loadEnvIfNeeded();
    }

    /**
     * Отправляет POST /webhook/max с секретом и payload
     */
    protected function postMaxWebhook(array $payload): \Psr\Http\Message\ResponseInterface
    {
        $secret = $this->getMaxWebhookSecret();
        return $this->httpClient()->post('/webhook/max', [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Max-Bot-Api-Secret' => $secret,
            ],
            RequestOptions::BODY => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * Генерирует payload для bot_started
     */
    protected function makeBotStartedPayload(int $userId = 0, int $chatId = -1, string $firstName = 'E2E Test'): array
    {
        return [
            'update_type' => 'bot_started',
            'timestamp' => (int)(microtime(true) * 1000),
            'chat_id' => $chatId,
            'user' => [
                'user_id' => $userId,
                'first_name' => $firstName,
                'is_bot' => false,
            ],
        ];
    }

    /**
     * Генерирует payload для bot_stopped
     */
    protected function makeBotStoppedPayload(int $userId = 0, int $chatId = -1): array
    {
        return [
            'update_type' => 'bot_stopped',
            'timestamp' => (int)(microtime(true) * 1000),
            'chat_id' => $chatId,
            'user' => [
                'user_id' => $userId,
                'first_name' => 'E2E Test',
                'is_bot' => false,
            ],
        ];
    }

    /**
     * Генерирует payload для message_created
     */
    protected function makeMessageCreatedPayload(
        string $text,
        int $userId = 0,
        int $chatId = -1,
        string $mid = '',
        string $chatType = 'dialog',
    ): array {
        if ($mid === '') {
            $mid = 'mid.e2e_' . bin2hex(random_bytes(8));
        }
        return [
            'update_type' => 'message_created',
            'timestamp' => (int)(microtime(true) * 1000),
            'message' => [
                'sender' => [
                    'user_id' => $userId,
                    'first_name' => 'E2E',
                    'last_name' => 'Test',
                    'is_bot' => false,
                ],
                'recipient' => [
                    'chat_id' => $chatId,
                    'chat_type' => $chatType,
                    'user_id' => $userId,
                ],
                'timestamp' => (int)(microtime(true) * 1000),
                'body' => [
                    'mid' => $mid,
                    'seq' => rand(1, 99999),
                    'text' => $text,
                ],
            ],
        ];
    }

    /**
     * Генерирует payload для message_callback
     * $callbackPayload передаётся как array — метод сам сделает json_encode
     */
    protected function makeMessageCallbackPayload(
        array $callbackPayload,
        int $userId = 0,
        int $chatId = -1,
        string $callbackId = '',
    ): array {
        if ($callbackId === '') {
            $callbackId = 'cb.e2e_' . bin2hex(random_bytes(8));
        }
        return [
            'update_type' => 'message_callback',
            'timestamp' => (int)(microtime(true) * 1000),
            'callback' => [
                'timestamp' => (int)(microtime(true) * 1000),
                'callback_id' => $callbackId,
                'payload' => json_encode($callbackPayload, JSON_UNESCAPED_UNICODE),
                'user' => [
                    'user_id' => $userId,
                    'first_name' => 'E2E',
                    'is_bot' => false,
                ],
            ],
            'message' => [
                'sender' => [
                    'user_id' => 999,
                    'first_name' => 'МКД Бот',
                    'is_bot' => true,
                ],
                'recipient' => [
                    'chat_id' => $chatId,
                    'chat_type' => 'dialog',
                    'user_id' => $userId,
                ],
                'timestamp' => (int)(microtime(true) * 1000),
                'body' => [
                    'mid' => 'mid.bot_msg_' . bin2hex(random_bytes(8)),
                    'seq' => 1,
                    'text' => '🏠 Я — МКД-Бот, хранитель канала!',
                ],
            ],
        ];
    }

    /**
     * Assertion: webhook принят (200) или Max API недоступен (500), но не 403/400
     */
    protected function assertWebhookAccepted(int $status, string $message = ''): void
    {
        $this->assertContains(
            $status,
            [200, 500],
            $message ?: "Webhook должен возвращать 200 (успех) или 500 (ошибка Max API), получен: {$status}",
        );
    }

    /**
     * Генерирует уникальный mid для тестов
     */
    protected function generateMid(): string
    {
        return 'mid.e2e_' . bin2hex(random_bytes(8));
    }

    /**
     * Генерирует уникальный callbackId для тестов
     */
    protected function generateCallbackId(): string
    {
        return 'cb.e2e_' . bin2hex(random_bytes(8));
    }
}
