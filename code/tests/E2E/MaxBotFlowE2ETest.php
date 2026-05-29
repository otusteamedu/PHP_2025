<?php

declare(strict_types=1);

namespace MkdBot\Tests\E2E;

/**
 * E2E-тесты бизнес-потоков МКД Чат-бота
 *
 * @group e2e
 */
class MaxBotFlowE2ETest extends BaseE2ETest
{
    /**
     * Поток: bot_stopped -> очистка состояния (всегда 200, не зависит от Max API)
     */
    public function testBotStoppedReturns200(): void
    {
        $payload = $this->makeBotStoppedPayload(userId: 0, chatId: -1);
        $response = $this->postMaxWebhook($payload);
        $this->assertEquals(200, $response->getStatusCode(), 'bot_stopped должен возвращать 200');
    }

    /**
     * Поток: Кнопка «УК» -> показ контактов управляющей компании
     */
    public function testContactsUkCallbackFlow(): void
    {
        $payload = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'contacts', 'type' => 'uk'],
            userId: 0,
            chatId: -1,
        );
        $response = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response->getStatusCode(), 'Кнопка УК: webhook принят');
    }

    /**
     * Поток: Кнопка «Совет дома» -> показ контактов совета дома
     */
    public function testContactsCouncilCallbackFlow(): void
    {
        $payload = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'contacts', 'type' => 'council'],
            userId: 0,
            chatId: -1,
        );
        $response = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response->getStatusCode(), 'Кнопка Совет дома: webhook принят');
    }

    /**
     * Поток: Кнопка «Вопрос ИИ» -> заглушка «Функция в разработке»
     */
    public function testRagQueryStubCallbackFlow(): void
    {
        $payload = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'rag_query', 'step' => 'menu'],
            userId: 0,
            chatId: -1,
        );
        $response = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response->getStatusCode(), 'Кнопка Вопрос ИИ: webhook принят');
    }

    /**
     * Поток: Полный flow «Улучшение бота» (Feature proposal)
     * Шаги: кнопка -> тема -> описание -> подтверждение
     */
    public function testFeatureProposalFullFlow(): void
    {
        $userId = 0;
        $chatId = -1;

        // Шаг 1: Нажатие кнопки «Улучшение бота»
        $step1 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'feature', 'step' => 'menu'],
            userId: $userId,
            chatId: $chatId,
        );
        $response1 = $this->postMaxWebhook($step1);
        $this->assertWebhookAccepted($response1->getStatusCode(), 'Шаг 1 — кнопка Feature: webhook принят');

        // Шаг 2: Ввод темы
        $step2 = $this->makeMessageCreatedPayload(
            text: 'Новая функция для бота',
            userId: $userId,
            chatId: $chatId,
        );
        $response2 = $this->postMaxWebhook($step2);
        $this->assertWebhookAccepted($response2->getStatusCode(), 'Шаг 2 — ввод темы: webhook принят');

        // Шаг 3: Ввод описания
        $step3 = $this->makeMessageCreatedPayload(
            text: 'Подробное описание новой функции для улучшения работы бота',
            userId: $userId,
            chatId: $chatId,
        );
        $response3 = $this->postMaxWebhook($step3);
        $this->assertWebhookAccepted($response3->getStatusCode(), 'Шаг 3 — ввод описания: webhook принят');

        // Шаг 4: Подтверждение
        $step4 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'confirm', 'type' => 'feature', 'step' => 'preview'],
            userId: $userId,
            chatId: $chatId,
        );
        $response4 = $this->postMaxWebhook($step4);
        $this->assertWebhookAccepted($response4->getStatusCode(), 'Шаг 4 — подтверждение: webhook принят');
    }

    /**
     * Поток: Полный flow «Предложение совету дома» (Suggestion proposal)
     */
    public function testSuggestionProposalFullFlow(): void
    {
        $userId = 0;
        $chatId = -1;

        // Шаг 1: Нажатие кнопки
        $step1 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'suggestion', 'step' => 'menu'],
            userId: $userId,
            chatId: $chatId,
        );
        $response1 = $this->postMaxWebhook($step1);
        $this->assertWebhookAccepted($response1->getStatusCode(), 'Шаг 1 — кнопка Suggestion: webhook принят');

        // Шаг 2: Ввод темы
        $step2 = $this->makeMessageCreatedPayload(
            text: 'Ремонт подъезда',
            userId: $userId,
            chatId: $chatId,
        );
        $response2 = $this->postMaxWebhook($step2);
        $this->assertWebhookAccepted($response2->getStatusCode(), 'Шаг 2 — ввод темы: webhook принят');

        // Шаг 3: Ввод описания
        $step3 = $this->makeMessageCreatedPayload(
            text: 'Нужно покрасить стены и заменить освещение в подъезде',
            userId: $userId,
            chatId: $chatId,
        );
        $response3 = $this->postMaxWebhook($step3);
        $this->assertWebhookAccepted($response3->getStatusCode(), 'Шаг 3 — ввод описания: webhook принят');

        // Шаг 4: Подтверждение
        $step4 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'confirm', 'type' => 'suggestion', 'step' => 'preview'],
            userId: $userId,
            chatId: $chatId,
        );
        $response4 = $this->postMaxWebhook($step4);
        $this->assertWebhookAccepted($response4->getStatusCode(), 'Шаг 4 — подтверждение: webhook принят');
    }

    /**
     * Поток: Отмена на шаге AwaitingSubject
     */
    public function testCancelOnAwaitingSubjectStep(): void
    {
        $userId = 0;
        $chatId = -1;

        // Шаг 1: Начало
        $step1 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'feature', 'step' => 'menu'],
            userId: $userId,
            chatId: $chatId,
        );
        $response1 = $this->postMaxWebhook($step1);
        $this->assertWebhookAccepted($response1->getStatusCode(), 'Шаг 1 — начало: webhook принят');

        // Шаг 2: Отмена
        $step2 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'cancel', 'step' => 'awaiting_subject'],
            userId: $userId,
            chatId: $chatId,
        );
        $response2 = $this->postMaxWebhook($step2);
        $this->assertWebhookAccepted($response2->getStatusCode(), 'Шаг 2 — отмена: webhook принят');
    }

    /**
     * Поток: Отмена на шаге AwaitingDescription
     */
    public function testCancelOnAwaitingDescriptionStep(): void
    {
        $userId = 0;
        $chatId = -1;

        // Шаг 1: Начало
        $step1 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'feature', 'step' => 'menu'],
            userId: $userId,
            chatId: $chatId,
        );
        $this->postMaxWebhook($step1);

        // Шаг 2: Ввод темы
        $step2 = $this->makeMessageCreatedPayload(
            text: 'Тема для отмены',
            userId: $userId,
            chatId: $chatId,
        );
        $this->postMaxWebhook($step2);

        // Шаг 3: Отмена
        $step3 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'cancel', 'step' => 'awaiting_description'],
            userId: $userId,
            chatId: $chatId,
        );
        $response3 = $this->postMaxWebhook($step3);
        $this->assertWebhookAccepted($response3->getStatusCode(), 'Шаг 3 — отмена на описании: webhook принят');
    }

    /**
     * Поток: Отмена на шаге Preview
     */
    public function testCancelOnPreviewStep(): void
    {
        $userId = 0;
        $chatId = -1;

        // Шаг 1: Начало
        $step1 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'feature', 'step' => 'menu'],
            userId: $userId,
            chatId: $chatId,
        );
        $this->postMaxWebhook($step1);

        // Шаг 2: Ввод темы
        $step2 = $this->makeMessageCreatedPayload(
            text: 'Тема для превью',
            userId: $userId,
            chatId: $chatId,
        );
        $this->postMaxWebhook($step2);

        // Шаг 3: Ввод описания
        $step3 = $this->makeMessageCreatedPayload(
            text: 'Описание для превью',
            userId: $userId,
            chatId: $chatId,
        );
        $this->postMaxWebhook($step3);

        // Шаг 4: Отмена на превью
        $step4 = $this->makeMessageCallbackPayload(
            callbackPayload: ['action' => 'cancel', 'step' => 'preview'],
            userId: $userId,
            chatId: $chatId,
        );
        $response4 = $this->postMaxWebhook($step4);
        $this->assertWebhookAccepted($response4->getStatusCode(), 'Шаг 4 — отмена на превью: webhook принят');
    }

    /**
     * Поток: Сообщение без активной сессии -> главное меню
     */
    public function testMessageWithoutSessionShowsMainMenu(): void
    {
        $payload = $this->makeMessageCreatedPayload(
            text: 'Привет',
            userId: 0,
            chatId: -1,
        );
        $response = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response->getStatusCode(), 'Сообщение без сессии: webhook принят');
    }

    /**
     * Поток: Команда /start -> главное меню
     */
    public function testStartCommandShowsMainMenu(): void
    {
        $payload = $this->makeMessageCreatedPayload(
            text: '/start',
            userId: 0,
            chatId: -1,
        );
        $response = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response->getStatusCode(), '/start: webhook принят');
    }

    /**
     * Поток: Команда /help -> главное меню
     */
    public function testHelpCommandShowsMainMenu(): void
    {
        $payload = $this->makeMessageCreatedPayload(
            text: '/help',
            userId: 0,
            chatId: -1,
        );
        $response = $this->postMaxWebhook($payload);
        $this->assertWebhookAccepted($response->getStatusCode(), '/help: webhook принят');
    }

    /**
     * Поток: Сообщение из канала -> дублирование в Telegram (ставится в RabbitMQ)
     */
    public function testChannelMessageTriggersForwardToTelegram(): void
    {
        $payload = $this->makeMessageCreatedPayload(
            text: 'Новость из канала МКД',
            userId: 100,
            chatId: -100,
            chatType: 'channel',
        );
        $response = $this->postMaxWebhook($payload);
        // Для channel-сообщений — 200 (ставится в RabbitMQ, не зависит от Max API)
        $this->assertEquals(200, $response->getStatusCode(), 'Сообщение из канала: должно вернуть 200');
    }
}
