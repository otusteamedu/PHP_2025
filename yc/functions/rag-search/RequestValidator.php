<?php

declare(strict_types=1);

class RequestValidator
{
    /**
     * Валидация входящего события Yandex Cloud Function.
     *
     * @param array $event Данные события от Yandex Cloud Functions
     * @return string|null Сообщение об ошибке или null, если валидация пройдена
     */
    public function validate(array $event): ?string
    {
        $method = $event['httpMethod']
            ?? $event['requestContext']['http']['method']
            ?? '';

        if ($method !== 'POST') {
            return 'Метод не разрешён. Используйте POST.';
        }

        $body = $event['body'] ?? '';
        if (empty($body)) {
            return 'Тело запроса пустое';
        }

        $decoded = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return 'Невалидный JSON в теле запроса';
        }

        if (!isset($decoded['question']) || trim($decoded['question']) === '') {
            return 'Поле "question" обязательно';
        }

        if (mb_strlen($decoded['question']) > 2000) {
            return 'Вопрос слишком длинный (максимум 2000 символов)';
        }

        return null; // Ошибок нет
    }
}
