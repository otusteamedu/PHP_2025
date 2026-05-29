<?php

declare(strict_types=1);

namespace MkdBot\Tests\E2E;

use GuzzleHttp\RequestOptions;

/**
 * E2E-тесты Telegram webhook-эндпоинта
 *
 * Проверяет авторизацию по секрету и приём валидного запроса
 * через реальный сервер.
 *
 * @group e2e
 */
class TelegramWebhookE2ETest extends BaseE2ETest
{
    /**
     * Общий payload для webhook-запросов
     */
    private function webhookPayload(): string
    {
        return json_encode([
            'update_id' => 0,
            'message' => [
                'message_id' => 0,
                'from' => ['id' => 0, 'is_bot' => false, 'first_name' => 'E2E'],
                'chat' => ['id' => -1, 'type' => 'private'],
                'date' => time(),
                'text' => '/start',
            ],
        ]);
    }

    /**
     * POST /webhook/telegram без заголовка X-Telegram-Bot-Api-Secret-Token -> 403
     *
     * Если TELEGRAM_SECRET_TOKEN не задан — middleware пропускает запрос,
     * поэтому тест пропускается с сообщением.
     */
    public function testWebhookWithoutSecretReturns403(): void
    {
        $secret = $this->getTelegramSecretToken();
        if ($secret === '') {
            $this->markTestSkipped(
                'TELEGRAM_SECRET_TOKEN не задан в .env — middleware не проверяет секрет',
            );
        }

        $response = $this->httpClient()->post('/webhook/telegram', [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                // Намеренно НЕ передаём X-Telegram-Bot-Api-Secret-Token
            ],
            RequestOptions::BODY => $this->webhookPayload(),
        ]);

        $status = $response->getStatusCode();
        $this->assertEquals(403, $status, 'Запрос без заголовка секрета должен возвращать 403');
    }

    /**
     * POST /webhook/telegram с правильным секретом + JSON -> 200
     *
     * Если секрет не задан — запрос отправляется без заголовка (middleware пропускает).
     */
    public function testWebhookWithValidSecretReturns200(): void
    {
        $secret = $this->getTelegramSecretToken();

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        // Добавляем секрет только если он задан
        if ($secret !== '') {
            $headers['X-Telegram-Bot-Api-Secret-Token'] = $secret;
        }

        $response = $this->httpClient()->post('/webhook/telegram', [
            RequestOptions::HEADERS => $headers,
            RequestOptions::BODY => $this->webhookPayload(),
        ]);

        $status = $response->getStatusCode();
        $this->assertEquals(200, $status, 'Webhook с правильным секретом должен возвращать 200');
    }
}
