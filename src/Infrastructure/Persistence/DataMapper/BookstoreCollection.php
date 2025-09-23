<?php

namespace BookstoreApp\Infrastructure\Persistence\DataMapper;

use ArrayIterator;
use BookstoreApp\Domain\Entity\Bookstore;
use IteratorAggregate;

class BookstoreCollection implements IteratorAggregate
{
    private array $bookstores;

    public function __construct(array $bookstores = [])
    {
        $this->bookstores = $bookstores;
    }

    public function add(Bookstore $bookstore): void
    {
        $this->bookstores[] = $bookstore;
    }

    public function get(int $index): ?Bookstore
    {
        return $this->bookstores[$index] ?? null;
    }

    public function remove(int $index): void
    {
        unset($this->bookstores[$index]);
        $this->bookstores = array_values($this->bookstores);
    }

    public function count(): int
    {
        return count($this->bookstores);
    }

    public function filterByCity(string $city): self
    {
        $filtered = array_filter($this->bookstores, fn($b) => $b->getCity() === $city);
        return new self(array_values($filtered));
    }

    public function filterByRating(float $minRating): self
    {
        $filtered = array_filter($this->bookstores, fn($b) => $b->getRating() >= $minRating);
        return new self(array_values($filtered));
    }

    public function getCities(): array
    {
        $cities = array_map(fn($b) => $b->getCity(), $this->bookstores);
        return array_unique($cities);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->bookstores);
    }

    public function toArray(): array
    {
        return array_map(fn($b) => $b->toArray(), $this->bookstores);
    }
}