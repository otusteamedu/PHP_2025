<?php

declare(strict_types=1);

class ResponseBuilder
{
    public const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR;

    public function success(SearchResponse $result): array
    {
        return [
            'statusCode' => 200,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($result->toArray(), self::JSON_FLAGS),
        ];
    }

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
            ], self::JSON_FLAGS),
        ];
    }
}
