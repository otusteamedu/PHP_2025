<?php

declare(strict_types=1);

namespace Queues\Infrastructure\Http;

use Queues\Application\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    public function json(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    public function html(string $template, array $params = []): void
    {
        header('Content-Type: text/html; charset=utf-8');

        if (is_file($template)) {
            extract($params, EXTR_SKIP);
            include $template;
            return;
        }
        echo $template;
    }

    public function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    public function withStatus(int $code): void
    {
        http_response_code($code);
    }
}
