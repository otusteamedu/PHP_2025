<?php

declare(strict_types=1);

namespace DataMapper;

class StorageAdapter
{
    public function __construct(private array $data){}

    
    public function find(int $id): array|null
    {
        if (isset($this->data[$id])) {
            return $this->data[$id];
        }

        return null;
    }

    
    public function findAll(): array
    {
        return array_values($this->data);
    }
} 