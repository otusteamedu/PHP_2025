<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Statement\RequestStatement;

class RequestStatementResponse
{
    public function __construct(
        public readonly int $jobId,
    )
    {

    }
}