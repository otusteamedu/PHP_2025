<?php
declare(strict_types=1);

namespace App\Http;

class Response
{
    private mixed $data;

    private int $status;

    private array $headers;

    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     */
    public function __construct(
        mixed $data,
        int $status = 200,
        array $headers = ['Content-Type' => 'application/json']
    ) {
        $this->data = $data;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * @param mixed $data
     * @param int $status
     * @return self
     */
    public static function json(mixed $data, int $status = 200): self
    {
        return new self($data, $status);
    }

    /**
     * @return void
     */
    public function sendHeaders(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
    }

    /**
     * @return void
     */
    public function send(): void
    {
        $this->sendHeaders();
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
}
