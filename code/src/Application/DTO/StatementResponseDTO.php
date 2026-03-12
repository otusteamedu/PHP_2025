<?php

declare(strict_types=1);

namespace Queues\Application\DTO;

use Queues\Domain\Enums\StatementStatus;

class StatementResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly StatementStatus $status,
        public readonly string $message
    ) {
    }
}
