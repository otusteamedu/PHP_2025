<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class CreateRequestDTO
{
    public function __construct(
        public string $data,
        public string $priority
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['data'] ?? '',
            $data['priority'] ?? 'normal'
        );
    }
}
