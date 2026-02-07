<?php

namespace Otus\Code\Domain\Model;

class Shop
{    
    public function __construct(private string $name, private int $stock) {}
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getStock(): int {
        return $this->stock;
    }
    
    public function isAvailable(): bool {
        return $this->stock > 0;
    }
    
    public static function fromArray(array $data): self  {
        return new self(
            $data['shop'] ?? '',
            $data['stock'] ?? 0
        );
    }
}