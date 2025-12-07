<?php
declare(strict_types=1);

namespace App\Http;

class Response
{
    private int $status;
    private array|string $content;
    private array $headers;

    /**
     * @param int $status
     * @param array|string $content
     * @param array $headers
     */
    public function __construct(
        int $status,
        array|string $content,
        array $headers
    ) {
        $this->status = $status;
        $this->content = $content;
        $this->headers = $headers;
    }

    /**
     * @param array $data
     * @param int $status
     * @return self
     */
    public static function json(array $data, int $status = 200): self
    {
        return new self(
            $status,
            json_encode($data),
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @param string $viewPath
     * @return self
     */
    public static function view(string $viewPath): self
    {
        return new self(
            200,
            file_get_contents($viewPath),
            ['Content-Type' => 'text/html']
        );
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
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
