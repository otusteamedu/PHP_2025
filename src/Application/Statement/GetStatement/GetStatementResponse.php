<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Statement\GetStatement;

class GetStatementResponse
{
    public function __construct(
        public readonly int $statementId,
        public readonly string $dateFrom,
        public readonly string $dateTo,
    )
    {

    }

    public function toArray(): array
    {
        return [
            'statementId' => $this->statementId,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ];
    }
}