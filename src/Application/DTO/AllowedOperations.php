<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class AllowedOperations
{
    private array $allowedOperations;

    public function __construct()
    {
        $this->allowedOperations = ['plus', 'minus', 'multiply', 'divide'];
    }

    public function getAllowedOperations(): array
    {
        return $this->allowedOperations;
    }
}
