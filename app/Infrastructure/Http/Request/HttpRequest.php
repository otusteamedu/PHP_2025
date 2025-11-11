<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Request;

final readonly class HttpRequest
{
    /**
     * Получает параметр из HTTP запроса
     */
    public function getParameter(string $name): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return (string) ($_POST[$name] ?? '');
        }

        return (string) ($_GET[$name] ?? '');
    }
}
