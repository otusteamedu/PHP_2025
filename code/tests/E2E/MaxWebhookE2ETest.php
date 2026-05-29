<?php

declare(strict_types=1);

namespace MkdBot\Tests\E2E;

use GuzzleHttp\RequestOptions;

/**
 * E2E-тесты Max webhook-эндпоинта
 *
 * Проверяет авторизацию по секрету, валидацию JSON
 * и обработку запросов через реальный сервер.
 *
 * @group e2e
 */
class MaxWebhookE2ETest extends BaseE2ETest
{
    /**
     * POST /webhook/max без заголовка X-Max-Bot-Api-Secret -> 403
     */
    public function testWebhookWithoutSecretReturns403(): void
    {
        $response = $this->httpClient()->post('/webhook/max', [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                // Намеренно НЕ передаём X-Max-Bot-Api-Secret
            ],
            RequestOptions::BODY => json_encode([
                'update_type' => 'bot_started',
                'timestamp' => (int)(microtime(true) * 1000),
                'chat_id' => -1,
                'user' => [
                    'user_id' => 0,
                    'first_name' => 'E2E',
                    'is_bot' => false,
                ],
            ]),
        ]);

        $status = $response->getStatusCode();
        $this->assertEquals(403, $status, 'Webhook без секрета должен возвращать 403');
    }

    /**
     * POST /webhook/max с правильным секретом + валидный JSON -> 200
     *
     * Используется событие bot_stopped — оно не вызывает внешние API
     * (только БД-операции: удаление состояния, деактивация подписчика),
     * поэтому не зависит от доступности Max API.
     *
     * Тестовый user_id=0 и chat_id=-1 чтобы не сломать реальные данные.
     */
    public function testWebhookWithValidSecretReturns200(): void
    {
        $secret = $this->getMaxWebhookSecret();
        if ($secret === '') {
            $this->markTestSkipped('MAX_WEBHOOK_SECRET не задан в .env');
        }

        // bot_stopped — не вызывает Max API, только БД-операции
        $payload = json_encode([
            'update_type' => 'bot_stopped',
            'timestamp' => (int)(microtime(true) * 1000),
            'chat_id' => -1,
            'user' => [
                'user_id' => 0,
                'first_name' => 'E2E Test',
                'is_bot' => false,
            ],
        ]);

        $response = $this->httpClient()->post('/webhook/max', [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Max-Bot-Api-Secret' => $secret,
            ],
            RequestOptions::BODY => $payload,
        ]);

        $status = $response->getStatusCode();
        $this->assertEquals(200, $status, 'Webhook с правильным секретом и валидным JSON должен возвращать 200');
    }

    /**
     * POST /webhook/max с секретом + невалидный JSON -> 400
     */
    public function testWebhookWithInvalidJsonReturns400(): void
    {
        $secret = $this->getMaxWebhookSecret();
        if ($secret === '') {
            $this->markTestSkipped('MAX_WEBHOOK_SECRET не задан в .env');
        }

        $response = $this->httpClient()->post('/webhook/max', [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Max-Bot-Api-Secret' => $secret,
            ],
            RequestOptions::BODY => '{invalid json content!!!',
        ]);

        $status = $response->getStatusCode();
        $this->assertEquals(400, $status, 'Webhook с невалидным JSON должен возвращать 400');
    }
}
