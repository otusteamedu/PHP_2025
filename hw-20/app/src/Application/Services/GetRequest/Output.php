<?php

declare(strict_types=1);

namespace App\Application\Services\GetRequest;

use JsonSerializable;

final readonly class Output implements JsonSerializable
{
    public function __construct(
        public int $requestId,
        public string $status,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'request_id' => $this->requestId,
            'status' => $this->status,
        ];
    }
}
