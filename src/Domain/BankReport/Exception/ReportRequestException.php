<?php

declare(strict_types=1);

namespace App\Domain\BankReport\Exception;

class ReportRequestException extends \RuntimeException
{
    public static function requestNotAccepted(\Throwable $e): self
    {
        return new self('Не удалось принять запрос. Попробуйте позже.', previous: $e);
    }
}
