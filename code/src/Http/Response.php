<?php

declare(strict_types=1);

namespace App\Http;

class Response
{
    /**
     * Ответ об ошибке
     *
     * @param int $code HTTP-код ответа
     * @param string $message Сообщение об ошибке
     * @return string Ответ
     */
    public function error(int $code, string $message): string
    {
        http_response_code($code);
        header('Content-Type: text/plain; charset=utf-8');
        
        return $message;
    }

    /**
     * Ответ об успехе
     *
     * @param array $sessionData Данные сессии
     * @return string Ответ
     */
    public function success(array $sessionData): string
    {
        http_response_code(200);
        header('Content-Type: text/plain; charset=utf-8');
        
        $lines = [];
        $lines[] = 'Всё хорошо. Контейнер: ' . ($sessionData['container'] ?? '-');
        $lines[] = 'PHPSESSID: ' . ($sessionData['session_id'] ?? '-');
        $lines[] = 'SESSION: ' . json_encode($sessionData, JSON_UNESCAPED_UNICODE);
        
        return implode(PHP_EOL, $lines) . PHP_EOL;
    }
}
