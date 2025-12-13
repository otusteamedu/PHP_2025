<?php

declare(strict_types=1);

namespace App\Http;

class Request
{
    private string $method;
    private string $path;
    private array $body;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $this->body = $this->parseJsonBody();
    }

    /**
     * Получает HTTP метод запроса
     * 
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Получает путь запроса
     * 
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
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

    /**
     * Проверяет что тело запроса - непустой массив emails
     *
     * @return bool
     */
    public function isValidEmailsBody(): bool
    {
        if (empty($this->body)) {
            return false;
        }

        return true;
    }
}
