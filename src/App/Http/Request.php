<?php
declare(strict_types=1);

namespace App\Http;

class Request
{
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getPostParam(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public function getRawInput(): string
    {
        return file_get_contents('php://input');
    }

}