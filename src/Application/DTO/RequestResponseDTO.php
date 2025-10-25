<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entity\Request;

final readonly class RequestResponseDTO
{
    public function __construct(
        public string $id,
        public string $status,
        public string $createdAt
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->id()->value(),
            $request->status()->value(),
            $request->createdAt()->format('c')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'createdAt' => $this->createdAt,
        ];
    }
}
