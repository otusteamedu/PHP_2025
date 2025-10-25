<?php

declare(strict_types=1);

namespace DataMapper;

class StorageAdapter
{
    /**
     * @param array<int, array<string, mixed>>
     */
    public function __construct(private array $data)
    {
    }

    public function find(int $id): array|null
    {
        if (isset($this->data[$id])) {
            return $this->data[$id];
        }

        return null;
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function findAll(): array
    {
        return array_values($this->data);
    }
} 