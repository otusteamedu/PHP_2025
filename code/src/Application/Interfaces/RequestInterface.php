<?php

declare(strict_types=1);

namespace Queues\Application\Interfaces;

interface RequestInterface
{
    public function get(string $key, ?string $default = null): ?string;

    public function post(string $key, ?string $default = null): ?string;

    public function json(): array;

    public function files(): array;

    public function method(): string;

    public function path(): string;
}
