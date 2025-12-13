<?php

declare(strict_types=1);

namespace App\Interfaces;

interface EmailValidatorInterface
{
    /**
     * Проверяет валидность одного email адреса
     * @param string $email Email адрес
     * @return bool true - если email валиден, false - если не валиден
     */
    public function verifyEmail(string $email): bool;

    /**
     * Проверяет валидность массива email адресов
     * @param array $emails Массив email адресов
     * @return array Массив с результатами проверки
     */
    public function verifyEmails(array $emails): array;
}
