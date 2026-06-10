<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use MkdBot\Domain\ValueObject\RagSearchResult;

interface RagSearchClientInterface
{
    /**
     * @throws \RuntimeException при ошибке связи с RAG-сервисом
     */
    public function search(string $question): RagSearchResult;
}
