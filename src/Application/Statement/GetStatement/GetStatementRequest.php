<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Statement\GetStatement;

class GetStatementRequest
{
    public function __construct(
        private readonly int $statementId,
    )
    {

    }

    public function getStatementId(): int
    {
        return $this->statementId;
    }
}