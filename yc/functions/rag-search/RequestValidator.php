<?php

declare(strict_types=1);

class ValidationResult
{
    public function __construct(
        public readonly ?string $error = null,
        public readonly int $errorCode = 400,
        public readonly ?array $decodedBody = null,
    ) {}

    public function isSuccess(): bool
    {
        return $this->error === null;
    }
}

class RequestValidator
{
    public function __construct(
        private readonly Config $config
    ) {}

    public function validate(array $event): ValidationResult
    {
        $method = $event['httpMethod']
            ?? $event['requestContext']['http']['method']
            ?? '';

        if ($method !== 'POST') {
            return new ValidationResult(
                error: 'Метод не разрешён. Используйте POST.',
                errorCode: 400,
            );
        }

        $authResult = $this->validateWebhookApiKey($event);
        if ($authResult !== null) {
            return $authResult;
        }

        $body = $event['body'] ?? '';
        if (empty($body)) {
            return new ValidationResult(error: 'Тело запроса пустое');
        }

        $decoded = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new ValidationResult(error: 'Невалидный JSON в теле запроса');
        }

        if (!isset($decoded['question']) || trim($decoded['question']) === '') {
            return new ValidationResult(error: 'Поле "question" обязательно');
        }

        if (mb_strlen($decoded['question']) > 2000) {
            return new ValidationResult(error: 'Вопрос слишком длинный (максимум 2000 символов)');
        }

        return new ValidationResult(decodedBody: $decoded);
    }

    private function validateWebhookApiKey(array $event): ?ValidationResult
    {
        $expectedKey = $this->config->webhookApiKey;

        if ($expectedKey === '') {
            return new ValidationResult(
                error: 'Api-ключ не настроен — авторизация запрещена',
                errorCode: 500,
            );
        }

        $headers = array_change_key_case($event['headers'] ?? []);
        $headerKey = $headers['x-api-key'] ?? '';

        if (!hash_equals($expectedKey, $headerKey)) {
            return new ValidationResult(
                error: 'Неверный API-ключ. Доступ запрещён.',
                errorCode: 401,
            );
        }

        return null;
    }
}
