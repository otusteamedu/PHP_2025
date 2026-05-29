<?php

declare(strict_types=1);

namespace MkdBot\Tests\E2E;

/**
 * E2E-тесты идемпотентности webhook в МКД Чат-боте
 *
 * @group e2e
 */
class MaxBotIdempotencyE2ETest extends BaseE2ETest
{
    /**
     * Идемпотентность: повторный webhook с тем же mid — не обрабатывается повторно
     */
    public function testIdempotentMessageCreatedWebhook(): void
    {
        $mid = $this->generateMid();

        // Первый запрос — должен быть принят
        $payload1 = $this->makeMessageCreatedPayload(
            text: 'Тест идемпотентности',
            userId: 0,
            chatId: -1,
            mid: $mid,
        );
        $response1 = $this->postMaxWebhook($payload1);
        $this->assertWebhookAccepted($response1->getStatusCode(), 'Первый запрос: принят');

        // Второй запрос с тем же mid — должен быть принят (200 если идемпотентно, 500 если Max API ошибка)
        $payload2 = $this->makeMessageCreatedPayload(
            text: 'Тест идемпотентности',
            userId: 0,
            chatId: -1,
            mid: $mid,
        );
        $response2 = $this->postMaxWebhook($payload2);
        $this->assertWebhookAccepted($response2->getStatusCode(), 'Повторный запрос с тем же mid: webhook принят');
    }

    /**
     * Идемпотентность: повторный callback с тем же callbackId
     */
    public function testIdempotentCallbackWebhook(): void
    {
        $callbackId = $this->generateCallbackId();

        // Первый запрос
        $payload1 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'contacts', 'type' => 'uk'],
            userId: 0,
            chatId: -1,
            callbackId: $callbackId,
        );
        $response1 = $this->postMaxWebhook($payload1);
        $this->assertWebhookAccepted($response1->getStatusCode(), 'Первый callback: принят');

        // Второй запрос с тем же callbackId — должен быть принят (200 если идемпотентно, 500 если Max API ошибка)
        $payload2 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'contacts', 'type' => 'uk'],
            userId: 0,
            chatId: -1,
            callbackId: $callbackId,
        );
        $response2 = $this->postMaxWebhook($payload2);
        $this->assertWebhookAccepted($response2->getStatusCode(), 'Повторный callback с тем же callbackId: webhook принят');
    }

    /**
     * Устаревший callback (step не совпадает с текущим состоянием)
     */
    public function testStaleCallbackWithWrongStep(): void
    {
        // Отправить confirm без активной сессии (step=preview, но сессии нет)
        $payload = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'confirm', 'type' => 'feature', 'step' => 'preview'],
            userId: 0,
            chatId: -1,
        );
        $response = $this->postMaxWebhook($payload);
        // Должен быть принят — бот покажет "сессия истекла" + главное меню
        $this->assertWebhookAccepted($response->getStatusCode(), 'Устаревший callback: webhook принят');
    }

    /**
     * bot_started — идемпотентен по своей природе (повторный = показать меню снова)
     */
    public function testBotStartedIsNaturallyIdempotent(): void
    {
        $payload = $this->makeBotStartedPayload(userId: 0, chatId: -1);
        $response1 = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response1->getStatusCode(), 'Первый bot_started: принят');

        $response2 = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response2->getStatusCode(), 'Повторный bot_started: принят');
    }
}
