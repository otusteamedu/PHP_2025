<?php

declare(strict_types=1);

namespace App\Domain\Service\Session;

interface SessionInterface
{
    /**
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void;

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @param string $key
     */
    public function remove(string $key): void;

    public function clear(): void;
}
