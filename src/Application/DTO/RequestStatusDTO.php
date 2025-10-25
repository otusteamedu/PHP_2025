<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entity\Request;

final readonly class RequestStatusDTO
{
    public function __construct(
        public string $id,
        public string $status,
        public string $createdAt,
        public ?string $processedAt = null,
        public ?string $result = null,
        public ?string $error = null,
        public ?int $processingTime = null
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $processingTime = null;
        if ($request->processedAt()) {
            $processingTime = $request->processedAt()->getTimestamp() - $request->createdAt()->getTimestamp();
        }

        return new self(
            $request->id()->value(),
            $request->status()->value(),
            $request->createdAt()->format('c'),
            $request->processedAt()?->format('c'),
            $request->result(),
            $request->error(),
            $processingTime
        );
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'status' => $this->status,
            'createdAt' => $this->createdAt,
        ];

        if ($this->processedAt !== null) {
            $data['processedAt'] = $this->processedAt;
        }

        if ($this->result !== null) {
            $data['result'] = $this->result;
        }

        if ($this->error !== null) {
            $data['error'] = $this->error;
        }

        if ($this->processingTime !== null) {
            $data['processingTime'] = $this->processingTime;
        }

        return $data;
    }
}
