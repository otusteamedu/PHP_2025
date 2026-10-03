<?php

declare(strict_types=1);

namespace App\Domain\BankReport\Exception;

class ClientNameException extends \InvalidArgumentException
{
    public static function notEmpty(): self
    {
        return new self('Имя клиента не может быть пустым.');
    }

    public static function tooShort(int $min): self
    {
        return new self("Имя клиента не может быть меньше $min символов.");
    }

    public static function tooLong(int $max): self
    {
        return new self("Имя клиента не может быть длиннее $max символов.");
    }
}
