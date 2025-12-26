<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Statement\RequestStatement;

class RequestStatementRequest
{
    public function __construct(
        public readonly string $dateFrom,
        public readonly string $dateTo,
    )
    {

    }
}