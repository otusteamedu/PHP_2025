<?php

declare(strict_types=1);

namespace Queues\Application\Interfaces;

interface ResponseInterface
{
    public function json(array $data): void;

    public function html(string $template, array $params = []): void;

    public function redirect(string $url): void;

    public function withStatus(int $code): void;
}
