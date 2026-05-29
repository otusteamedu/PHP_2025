<?php

declare(strict_types=1);

namespace MkdBot\Tests\E2E;

/**
 * E2E-тесты валидации ввода в МКД Чат-боте
 *
 * @group e2e
 */
class MaxBotValidationE2ETest extends BaseE2ETest
{
    /**
     * Валидация: тема превышает 200 символов
     */
    public function testSubjectExceedsMaxLength(): void
    {
        $userId = 0;
        $chatId = -1;

        // Начать ввод предложения
        $start = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'feature', 'step' => 'menu'],
            userId: $userId,
            chatId: $chatId,
        );
        $this->postMaxWebhook($start);

        // Отправить тему > 200 символов
        $longSubject = str_repeat('А', 201);
        $payload = $this->makeMessageCreatedPayload(
            text: $longSubject,
            userId: $userId,
            chatId: $chatId,
        );
        $response = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response->getStatusCode(), 'Тема > 200 символов: webhook принят');
    }

    /**
     * Валидация: описание превышает 3000 символов
     */
    public function testDescriptionExceedsMaxLength(): void
    {
        $userId = 0;
        $chatId = -1;

        // Начать ввод предложения
        $start = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'feature', 'step' => 'menu'],
            userId: $userId,
            chatId: $chatId,
        );
        $this->postMaxWebhook($start);

        // Ввести корректную тему
        $subject = $this->makeMessageCreatedPayload(
            text: 'Тема для теста длины описания',
            userId: $userId,
            chatId: $chatId,
        );
        $this->postMaxWebhook($subject);

        // Отправить описание > 3000 символов
        $longDescription = str_repeat('Б', 3001);
        $payload = $this->makeMessageCreatedPayload(
            text: $longDescription,
            userId: $userId,
            chatId: $chatId,
        );
        $response = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response->getStatusCode(), 'Описание > 3000 символов: webhook принят');
    }

    /**
     * Тема ровно 200 символов — допустимо
     */
    public function testSubjectExactlyMaxLength(): void
    {
        $userId = 0;
        $chatId = -1;

        $start = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'feature', 'step' => 'menu'],
            userId: $userId,
            chatId: $chatId,
        );
        $this->postMaxWebhook($start);

        $exactSubject = str_repeat('В', 200);
        $payload = $this->makeMessageCreatedPayload(
            text: $exactSubject,
            userId: $userId,
            chatId: $chatId,
        );
        $response = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response->getStatusCode(), 'Тема = 200 символов: webhook принят');
    }

    /**
     * Описание ровно 3000 символов — допустимо
     */
    public function testDescriptionExactlyMaxLength(): void
    {
        $userId = 0;
        $chatId = -1;

        $start = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'feature', 'step' => 'menu'],
            userId: $userId,
            chatId: $chatId,
        );
        $this->postMaxWebhook($start);

        $subject = $this->makeMessageCreatedPayload(
            text: 'Тема',
            userId: $userId,
            chatId: $chatId,
        );
        $this->postMaxWebhook($subject);

        $exactDescription = str_repeat('Г', 3000);
        $payload = $this->makeMessageCreatedPayload(
            text: $exactDescription,
            userId: $userId,
            chatId: $chatId,
        );
        $response = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response->getStatusCode(), 'Описание = 3000 символов: webhook принят');
    }
}
