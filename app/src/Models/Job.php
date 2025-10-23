<?php

namespace App\Models;

class Job
{
    public function __construct(
        public string $id,
        public string $status,
        public array $data,
        public ?string $result = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'data' => $this->data,
            'result' => $this->result,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}