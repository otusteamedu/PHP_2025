<?php

declare(strict_types=1);

namespace Otus\Kernel;

class ValueObject
{
    /**
     * @param array $list
     */
    public function __construct(
        protected array $list,
    ) {
    }

    /**
     * @param string $name
     * @param string|null $default
     *
     * @return string|null
     */
    public function get(string $name, ?string $default = null): ?string
    {
        return $this->list[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param string|null $value
     */
    public function set(string $name, ?string $value): void
    {
        $this->list[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->list);
    }
}
