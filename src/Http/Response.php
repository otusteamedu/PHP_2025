<?php

namespace App\Http;

class Response
{
    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var string
     */
    private string $message;

    /**
     * @var array
     */
    private array $data;

    /**
     * @param array $data
     * @param int $statusCode
     */
    public function __construct(array $data = [], int $statusCode = 200)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->message = '';
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return void
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return void
     */
    public function send(): void
    {
        header("Content-Type: application/json");

        http_response_code($this->getStatusCode());

        echo json_encode($this->prepare(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return array|string
     */
    private function prepare(): array|string
    {
        return array_values(array_filter([$this->getMessage(), $this->getData()]))[0] ?? [];
    }
}