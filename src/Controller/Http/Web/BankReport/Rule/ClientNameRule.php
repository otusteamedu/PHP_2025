<?php

declare(strict_types=1);

namespace App\Controller\Http\Web\BankReport\Rule;

class ClientNameRule implements RuleInterface
{
    public function validate(mixed $data, array &$errors): void
    {
        $clientName = trim($data['client_name'] ?? '');

        if ($clientName === '') {
            $errors['client_name'][] = 'Поле "ФИО клиента" обязательно для заполнения.';
            return;
        }

        $length = mb_strlen($clientName);
        if ($length < 5 || $length > 50) {
            // В реальной системе это поле приходит из профиля пользователя.
            // Здесь - минимальная защита от мусора.
            $errors['client_name'][] = 'ФИО должно содержать минимум от 5 до 50 символов.';
        }
    }
}
