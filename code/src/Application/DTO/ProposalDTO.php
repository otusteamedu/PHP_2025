<?php

declare(strict_types=1);

namespace MkdBot\Application\DTO;

use MkdBot\Domain\Enum\ProposalType;

/**
 * DTO предложения для передачи между слоями
 */
class ProposalDTO
{
    public function __construct(
        public readonly ProposalType $type,
        public readonly int $userId,
        public readonly string $userName,
        public readonly string $subject,
        public readonly string $content,
    ) {
    }
}
