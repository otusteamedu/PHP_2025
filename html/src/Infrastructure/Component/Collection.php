<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Component;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

final class Collection implements Countable, IteratorAggregate
{
    /**
     * @param array $items
     */
    public function __construct(
        private array $items = [],
    ) {
    }

    /**
     * @param array $items
     *
     * @return self
     */
    public static function make(array $items = []): self
    {
        return new self($items);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @param int|string $key
     * @param mixed|null $default
     *
     * @return Collection
     */
    public function wrap(int|string $key, mixed $default = null): self
    {
        return self::make($this->get($key, $default));
    }

    /**
     * @param int|string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(int|string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return $this->items[$key];
        }

        return $default;
    }

    /**
     * @param int|string $key
     * @param mixed $value
     *
     * @return self
     */
    public function set(int|string $key, mixed $value): self
    {
        $this->items[$key] = $value;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function push(mixed $value): self
    {
        $this->items[] = $value;

        return $this;
    }

    /**
     * @param int|string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function pull(int|string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);

        $this->remove($key);

        return $value;
    }

    /**
     * @param int|string $key
     *
     * @return bool
     */
    public function has(int|string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * @param int|string $key
     *
     * @return self
     */
    public function remove(int|string $key): self
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function contains(mixed $value): bool
    {
        return in_array($value, $this->items, true);
    }

    /**
     * @return mixed
     */
    public function first(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        reset($this->items);

        return current($this->items);
    }

    /**
     * @return mixed
     */
    public function last(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        end($this->items);

        return current($this->items);
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->items);
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return array_values($this->items);
    }

    /**
     * @param callable $callback
     *
     * @return self
     */
    public function map(callable $callback): self
    {
        $keys = array_keys($this->items);
        $values = array_map($callback, $this->items, $keys);

        return new self(array_combine($keys, $values));
    }

    /**
     * @param callable $callback
     *
     * @return self
     */
    public function filter(callable $callback): self
    {
        return new self(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @param callable $callback
     * @param mixed $initial
     *
     * @return mixed
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        $result = $initial;

        foreach ($this->items as $key => $value) {
            $result = $callback($result, $value, $key);
        }

        return $result;
    }

    /**
     * @param callable $callback
     *
     * @return self
     */
    public function each(callable $callback): self
    {
        foreach ($this->items as $key => $value) {
            $callback($value, $key);
        }

        return $this;
    }

    /**
     * @param array $items
     *
     * @return self
     */
    public function merge(array $items): self
    {
        return new self(array_merge($this->items, $items));
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
