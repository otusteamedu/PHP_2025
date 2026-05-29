<?php

declare(strict_types=1);

namespace MkdBot\Application\DTO;

/**
 * DTO RAG-запроса (v2 — заглушка в v1)
 */
class RagQueryDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $question,
    ) {
    }
}
