<?php

declare(strict_types=1);

class ResponseBuilder
{
    // Формирование успешного HTTP-ответа для Yandex Cloud Functions
    public function success(SearchResponse $result): array
    {
        return [
            'statusCode' => 200,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($result->toArray(), JSON_UNESCAPED_UNICODE),
        ];
    }

    // Формирование HTTP-ответа с ошибкой для Yandex Cloud Functions
    public function error(int $code, string $message): array
    {
        return [
            'statusCode' => $code,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode([
                'success' => false,
                'error' => [
                    'code' => $code,
                    'message' => $message,
                ],
            ], JSON_UNESCAPED_UNICODE),
        ];
    }
}
