<?php
declare(strict_types=1);

namespace App\Http;

class Response
{
    private readonly int $status;
    private readonly string $body;

    /**
     * @param $status
     * @param $body
     */
    public function __construct(
        $status,
        $body
    ) {
        $this->status = $status;
        $this->body = $body;
    }

    /**
     * @return void
     */
    public function send(): void
    {
        http_response_code($this->status);
        header('Content-Type: text/plain');
        echo $this->body;
    }
}
