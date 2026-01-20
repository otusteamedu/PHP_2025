<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

interface ValidatorInterface
{
    /**
     * @param mixed $value
     * @param string $fieldName
     * @return bool
     */
    public function validate(mixed $value, string $fieldName): bool;

    /**
     * @return string
     */
    public function getError(): string;
}
