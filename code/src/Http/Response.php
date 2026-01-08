<?php

declare(strict_types=1);

namespace App\Http;

class Response
{
    /**
     * Ответ в формате JSON
     *
     * @param int $code HTTP-код ответа
     * @param array $data Данные для ответа
     * @return string JSON ответ
     */
    public function json(int $code, array $data): string
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Ответ с ошибкой
     *
     * @param int $code HTTP-код ответа
     * @param string $message Сообщение об ошибке
     * @return string JSON ответ
     */
    public function error(int $code, string $message): string
    {
        return $this->json($code, [
            'success' => false,
            'error' => $message
        ]);
    }

    /**
     * Успешный ответ
     *
     * @param array $data Данные для ответа
     * @return string JSON ответ
     */
    public function success(array $data, int $code = 200): string
    {
        return $this->json($code, [
            'success' => true,
            'data' => $data
        ]);
    }
}
