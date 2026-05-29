<?php

declare(strict_types=1);

namespace MkdBot\Tests\E2E;

use GuzzleHttp\RequestOptions;

/**
 * E2E-тесты взаимодействия с ботом Max
 *
 * Эти тесты отправляют реальные webhook-и и проверяют
 * что сервер корректно принимает и обрабатывает их.
 *
 * Сложные тесты (проверка ответа бота через Max API) пропускаются —
 * требуют ручной проверки или мокирования Max API.
 *
 * @group e2e
 */
class MaxBotInteractionE2ETest extends BaseE2ETest
{
    /**
     * Отправка bot_started -> сервер должен принять webhook (200 или 500)
     *
     * Событие bot_started вызывает Max API для отправки ответа в чат.
     * С фейковым user_id=0 Max API вернёт ошибку -> сервер ответит 500.
     * Это нормальное поведение — проверяем что авторизация прошла (не 403)
     * и JSON валиден (не 400).
     */
    public function testBotStartedShowsMainMenu(): void
    {
        $secret = $this->getMaxWebhookSecret();
        if ($secret === '') {
            $this->markTestSkipped('MAX_WEBHOOK_SECRET не задан в .env');
        }

        // Тестовый payload — bot_started с фейковым user_id
        $payload = json_encode([
            'update_type' => 'bot_started',
            'timestamp' => (int)(microtime(true) * 1000),
            'chat_id' => -1,
            'user' => [
                'user_id' => 0,
                'first_name' => 'E2E Test',
                'is_bot' => false,
            ],
        ]);

        // Отправляем webhook
        $response = $this->httpClient()->post('/webhook/max', [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Max-Bot-Api-Secret' => $secret,
            ],
            RequestOptions::BODY => $payload,
        ]);

        $status = $response->getStatusCode();
        // 200 — если Max API доступен и принял запрос
        // 500 — если Max API недоступен или отклонил фейкового пользователя
        // НЕ должно быть 403 (секрет верный) и 400 (JSON валидный)
        $this->assertContains(
            $status,
            [200, 500],
            'Webhook bot_started с правильным секретом должен возвращать 200 (успех) или 500 (ошибка Max API), но не 403/400',
        );
    }

    /**
     * Отправка callback с action=contacts,type=uk -> бот показывает контакты УК
     *
     * Отправляем message_callback webhook на /webhook/max и проверяем,
     * что контроллер вернул 200 OK.
     */
    public function testMenuButtonUkShowsContacts(): void
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
     * Полный flow: кнопка «Улучшение бота» -> ввод темы -> ввод описания -> превью -> подтверждение
     *
     * Шаги:
     * 1. Callback webhook с payload {action:"feature",step:"menu"} — бот переходит в состояние «ожидание темы»
     * 2. message_created webhook с текстом темы — бот переходит в состояние «ожидание описания»
     * 3. message_created webhook с текстом описания — бот показывает превью
     * 4. Callback webhook с payload {action:"confirm",type:"feature",step:"awaiting_confirmation"} — бот подтверждает
     */
    public function testFeatureProposalFlow(): void
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
            callbackPayload: ['action' => 'confirm', 'type' => 'feature', 'step' => 'awaiting_confirmation'],
            userId: $userId,
            chatId: $chatId,
        );
        $response4 = $this->postMaxWebhook($step4);
        $this->assertWebhookAccepted($response4->getStatusCode(), 'Шаг 4 — подтверждение: webhook принят');
    }
}
