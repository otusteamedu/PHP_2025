<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Queue;

final class Payload
{
    /**
     * @param array $data
     */
    public function __construct(private array $data = [])
    {
    }

    /**
     * @param string $key
     *
     * @return string|int
     */
    public function get(string $key): string|int
    {
        return $this->data[$key];
    }

    /**
     * @param string $key
     * @param string|int $value
     *
     * @return self
     */
    public function set(string $key, string|int $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }
}
