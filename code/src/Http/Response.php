<?php

declare(strict_types=1);

namespace App\Http;

class Response
{
    /**
     * Ответ
     *
     * @param int $code HTTP-код ответа
     * @param string $message Сообщение
     * @return string Ответ
     */
    public function send(int $code, string $message): string
    {
        http_response_code($code);
        header('Content-Type: text/plain; charset=utf-8');

        return $message;
    }
}
