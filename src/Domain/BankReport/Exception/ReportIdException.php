<?php

declare(strict_types=1);

namespace App\Domain\BankReport\Exception;

class ReportIdException extends \InvalidArgumentException
{
    public static function notEmpty(): self
    {
        return new self('Идентификатор отчёта не может быть пустым.');
    }
}
