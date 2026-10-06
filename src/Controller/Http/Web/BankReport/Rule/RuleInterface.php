<?php

declare(strict_types=1);

namespace App\Controller\Http\Web\BankReport\Rule;

interface RuleInterface
{
    /**
     * Проверяет данные. Если невалидно - добавляет ошибку в $errors.
     */
    public function validate(array $data, array &$errors): void;
}
