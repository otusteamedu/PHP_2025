<?php

declare(strict_types=1);

namespace Ak\Hw\Application;

class Validator
{
    public const E_INVALID_FORMAT = 'INVALID_FORMAT';
    public const E_IS_EMPTY = 'IS_EMPTY';
    public const E_NOT_NUMERIC = 'NOT_NUMERIC';
    public const E_NOT_STRING = 'NOT_STRING';
    public const E_DATE_EXPIRED = 'DATE_EXPIRED';
    public const E_VALUE_NOT_POSITIVE = 'VALUE_NOT_POSITIVE';

    public function validate(array $data): array
    {
        $errors = [];

        // Card Number
        if (empty($data['card_number'])) {
            $errors['card_number'] = ['code' => self::E_IS_EMPTY, 'message' => 'Card number is required'];
        } elseif (!is_string($data['card_number']) || !preg_match('/^\d{4} \d{4} \d{4} \d{4}$/', $data['card_number'])) {
            $errors['card_number'] = ['code' => self::E_INVALID_FORMAT, 'message' => 'Invalid card number format (e.g., 4111 1111 1111 1111)'];
        }

        // Card Holder
        if (empty($data['card_holder'])) {
            $errors['card_holder'] = ['code' => self::E_IS_EMPTY, 'message' => 'Card holder is required'];
        } elseif (!is_string($data['card_holder']) || !preg_match('/^[a-zA-Z\s\'-]+$/', $data['card_holder'])) {
            $errors['card_holder'] = ['code' => self::E_INVALID_FORMAT, 'message' => 'Invalid characters in card holder name'];
        } elseif (substr_count($data['card_holder'], ' ') > 1) {
            $errors['card_holder'] = ['code' => self::E_INVALID_FORMAT, 'message' => 'Card holder name can contain at most one space'];
        }

        // Card Expiration
        if (empty($data['card_expiration'])) {
            $errors['card_expiration'] = ['code' => self::E_IS_EMPTY, 'message' => 'Expiration date is required'];
        } elseif (!is_string($data['card_expiration']) || !preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $data['card_expiration'], $matches)) {
            $errors['card_expiration'] = ['code' => self::E_INVALID_FORMAT, 'message' => 'Invalid expiration date format (MM/YY)'];
        } else {
            $expMonth = (int)$matches[1];
            $expYear = (int)$matches[2] + 2000;
            $currentMonth = (int)date('m');
            $currentYear = (int)date('Y');
            if ($expYear < $currentYear || ($expYear === $currentYear && $expMonth < $currentMonth)) {
                $errors['card_expiration'] = ['code' => self::E_DATE_EXPIRED, 'message' => 'Card has expired'];
            }
        }

        // CVV
        if (empty($data['cvv'])) {
            $errors['cvv'] = ['code' => self::E_IS_EMPTY, 'message' => 'CVV is required'];
        } elseif (!is_string($data['cvv']) || !preg_match('/^\d{3,4}$/', $data['cvv'])) {
            $errors['cvv'] = ['code' => self::E_INVALID_FORMAT, 'message' => 'Invalid CVV format (3 or 4 digits)'];
        }

        // Sum
        if (!isset($data['sum']) || $data['sum'] === '') {
            $errors['sum'] = ['code' => self::E_IS_EMPTY, 'message' => 'Sum is required'];
        } elseif (!is_numeric($data['sum'])) {
            $errors['sum'] = ['code' => self::E_NOT_NUMERIC, 'message' => 'Sum must be a number'];
        } elseif ((float)$data['sum'] <= 0) {
            $errors['sum'] = ['code' => self::E_VALUE_NOT_POSITIVE, 'message' => 'Sum must be a positive number'];
        }

        // Order Number
        if (!isset($data['order_number']) || trim((string)$data['order_number']) === '') {
            $errors['order_number'] = ['code' => self::E_IS_EMPTY, 'message' => 'Order number is required'];
        }

        return $errors;
    }
}
