<?php

namespace Validation\Email;

interface EmailValidatorInterface
{
    /**
     * Проверяет список email-адресов и возвращает массив невалидных.
     *
     * @param string[] $emails Список email'ов для проверки
     */
    public function isValid(array $emails): array;
}