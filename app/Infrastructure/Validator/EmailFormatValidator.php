<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Domain\Email\Email;
use App\Domain\Validator\ValidatorInterface;

class EmailFormatValidator implements ValidatorInterface
{
    private const string EMAIL_REGEX = '/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/';

    /**
     * @inheritdoc
     */
    public function isValid(Email $email): bool
    {
        return (bool) preg_match(self::EMAIL_REGEX, $email->getValue());
    }
}
