<?php declare(strict_types=1);

namespace App\Http;

class Response
{
    private array $data;
    private int $statusCode;
    private array $headers = [];

    public function __construct(array $data = [], int $statusCode = 200)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->setHeader('Content-Type', 'application/json');
    }

    /**
     * Устанавливает данные ответа.
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Возвращает данные ответа.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Устанавливает HTTP-статус код ответа.
     *
     * @param int $statusCode
     * @return self
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Возвращает HTTP-статус код ответа.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Устанавливает заголовок ответа.
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Возвращает заголовки ответа.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Отправляет ответ клиенту.
     *
     * @return void
     * @throws \JsonException
     */
    public function send(): void
    {
        // Устанавливаем HTTP-статус код
        http_response_code($this->statusCode);

        // Устанавливаем заголовки
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Кодируем данные в JSON и отправляем
        echo json_encode($this->data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Преобразует объект Response в строку (JSON).
     *
     * @return string
     * @throws \JsonException
     */
    public function __toString(): string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}