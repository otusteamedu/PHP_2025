<?php

declare(strict_types=1);

namespace App\Domain\Validator;

use App\Domain\Email\Email;

interface ValidatorInterface
{
    /**
     * Проверяет валидность email
     */
    public function isValid(Email $email): bool;
}
