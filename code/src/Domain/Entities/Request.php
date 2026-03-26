<?php

declare(strict_types=1);

namespace Api\Domain\Entities;

use Api\Domain\Enums\RequestStatus;

final class Request
{
    public function __construct(
        public readonly int $id,
        public readonly RequestStatus $status,
        public readonly string $content,
        public readonly \DateTimeImmutable $created,
        public readonly ?\DateTimeImmutable $processed = null,
        public readonly ?string $result = null
    ) {
    }

    public function setStatus(
        ?RequestStatus $status = null,
        ?string $result = null,
        ?\DateTimeImmutable $processed = null
    ): self {
        return new self(
            $this->id,
            $status ?? $this->status,
            $this->content,
            $this->created,
            $processed ?? $this->processed,
            $result ?? $this->result
        );
    }
}
