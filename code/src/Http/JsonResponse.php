<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Отправитель JSON-ответов
 */
final class JsonResponse
{
    /**
     * Отправляет JSON-ответ с указанным HTTP-кодом
     *
     * @param array<string, mixed> $data Данные ответа
     * @param int $statusCode HTTP-код ответа
     *
     * @return void
     */
    public function send(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
