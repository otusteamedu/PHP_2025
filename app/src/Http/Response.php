<?php
declare(strict_types=1);

namespace App\Http;

class Response
{
    private string $result;

    public function send(string $result, int $status): Response
    {
        http_response_code($status);
        $this->result = $result . ' ' . sprintf('(Container name: %s).', $_SERVER['HOSTNAME']);

        return $this;
    }

    public function __toString(): string
    {
        return $this->result;
    }
}