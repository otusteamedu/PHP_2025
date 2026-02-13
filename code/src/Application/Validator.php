<?php

declare(strict_types=1);

namespace App\Application;

class Validator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['card_number']) || !preg_match('/^\d{4} \d{4} \d{4} \d{4}$/', $data['card_number'])) {
            $errors['card_number'] = 'Invalid card number';
        }

        if (empty($data['card_holder']) || !is_string($data['card_holder'])) {
            $errors['card_holder'] = 'Invalid card holder';
        }

        if (empty($data['card_expiration']) || !preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $data['card_expiration'])) {
            $errors['card_expiration'] = 'Invalid card expiration';
        }

        if (empty($data['cvv']) || !preg_match('/^\d{3}$/', $data['cvv'])) {
            $errors['cvv'] = 'Invalid cvv';
        }

        if (empty($data['sum']) || !is_numeric($data['sum'])) {
            $errors['sum'] = 'Invalid sum';
        }

        if (empty($data['order_number']) || !is_string($data['order_number'])) {
            $errors['order_number'] = 'Invalid order number';
        }

        return $errors;
    }
}
