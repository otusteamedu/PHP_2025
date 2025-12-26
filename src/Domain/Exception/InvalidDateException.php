<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\Exception;

use InvalidArgumentException;

class InvalidDateException extends InvalidArgumentException
{
    public static function forInvalidFormat(string $dateString, string $format): self
    {
        return new self(sprintf(
            'The provided date string "%s" is not in the expected format "%s".',
            $dateString,
            $format
        ));
    }
}