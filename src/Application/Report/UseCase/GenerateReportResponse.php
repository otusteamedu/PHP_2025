<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Application\Report\UseCase;

class GenerateReportResponse
{
    public function __construct(
        public readonly string $message = "",
    )
    {

    }

    public function getMessage(): string
    {
        return $this->message;
    }
}