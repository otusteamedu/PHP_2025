<?php

declare(strict_types=1);

namespace Ak\Hw\Validation;

use DateTime;

final class DateValidator
{
    /**
     * @param string $date
     * @param string $format
     * @return bool
     */
    public function isValid(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
