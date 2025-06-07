<?php

declare(strict_types=1);

namespace App\Application;

class Request
{
    public function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getRequestPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * @throws \Exception
     */
    public function getPayload(): array
    {
        $content = file_get_contents('php://input');
        if ($content === false) {
            throw new \Exception('Couldn\'t get data from request body', 400);
        }

        try {
            return json_decode($content, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \Exception($e->getMessage(), 400);
        }
    }
}
