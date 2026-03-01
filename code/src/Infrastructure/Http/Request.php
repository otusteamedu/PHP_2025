<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

class Request
{
    private array $server;
    private array $body;

    public function __construct(array $server = [])
    {
        $this->server = $server;
        $this->body = $this->parseJsonBody();
    }

    public static function init(): self
    {
        return new self($_SERVER);
    }

    /**
     * Получает HTTP метод запроса
     * 
     * @return string
     */
    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Получает путь запроса
     * 
     * @return string
     */
    public function getPath(): string
    {
        return parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    }

    /**
     * Проверяет, является ли запрос POST
     * 
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Проверяет, является ли запрос GET
     * 
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * Получает тело запроса
     * 
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Получает значение из тела запроса по ключу,
     * если ключ не найден, возвращает null
     * 
     * @param string $key
     * @return mixed
     */
    public function getParamFromBody(string $key): mixed
    {
        return $this->body[$key] ?? null;
    }

    /**
     * Парсит JSON из тела запроса
     * 
     * @return array
     */
    private function parseJsonBody(): array
    {
        $json_data = file_get_contents('php://input');

        $data = json_decode($json_data, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        return is_array($data) ? $data : [];
    }
}
