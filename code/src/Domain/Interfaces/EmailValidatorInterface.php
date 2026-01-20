<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

interface EmailValidatorInterface
{
    /**
     * Проверяет валидность одного email адреса
     * @param mixed $email Email адрес
     * @return bool true - если email валиден, false - если не валиден
     */
    public function validate(mixed $email): bool;

    /**
     * @return string
     */
    public function getError(): string;
}
