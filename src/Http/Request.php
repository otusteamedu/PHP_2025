<?php declare(strict_types=1);

namespace App\Http;

class Request
{
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getPostData(): array
    {
        return $_POST;
    }

    public function getPostValue($key): ?string
    {
        return $this->getPostData()[$key] ?? null;
    }
}
