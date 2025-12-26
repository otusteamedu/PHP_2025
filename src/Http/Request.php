<?php
declare(strict_types=1);

namespace App\Http;

class Request
{
    /**
     * @return string
     */
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * @return array
     */
    public function json(): array
    {
        return json_decode(file_get_contents('php://input'),true) ?? [];
    }
}
