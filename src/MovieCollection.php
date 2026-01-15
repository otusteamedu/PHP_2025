<?php

namespace Arlex2305k\PatternsDb;

class MovieCollection implements \Iterator, \Countable, \ArrayAccess
{
	private array $items;
	private int $position;

	public function __construct(array $items = [])
	{
		$this->items = $items;
		$this->position = 0;
	}

	public function add(Movie $item): void
	{
		$this->items[] = $item;
	}

	public function addMany(array $items): void
	{
		foreach ($items as $item) {
			if ($item instanceof Movie) {
				$this->add($item);
			}
		}
	}

	public function get(int $index): ?Movie
	{
		return $this->items[$index] ?? null;
	}

	public function getAll(): array
	{
		return $this->items;
	}

	public function isEmpty(): bool
	{
		return empty($this->items);
	}

	// --- Countable
	public function count(): int
	{
		return count($this->items);
	}

	// --- Iterator
	public function rewind(): void
	{
		$this->position = 0;
	}

	public function valid(): bool
	{
		return isset($this->items[$this->position]);
	}

	public function key(): int
	{
		return $this->position;
	}

	public function next(): void
	{
		++$this->position;
	}

	public function current(): Movie
	{
		return $this->items[$this->position];
	}

	// --- ArrayAccess
	public function offsetExists($offset): bool
	{
		return isset($this->items[$offset]);
	}

	public function offsetGet($offset): ?Movie
	{
		return $this->items[$offset] ?? null;
	}

	public function offsetSet($offset, $value): void
	{
		if (!$value instanceof Movie) {
			throw new \InvalidArgumentException('Можно добавлять только объекты типа Movie');
		}

		if (is_null($offset)) {
			$this->items[] = $value;
		} else {
			$this->items[$offset] = $value;
		}
	}

	public function offsetUnset($offset): void
	{
		unset($this->items[$offset]);
	}
}
