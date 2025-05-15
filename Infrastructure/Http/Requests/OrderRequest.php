<?php

declare(strict_types=1);

namespace Infrastructure\Http\Requests;

class OrderRequest
{
    /**
     * @param array $data
     * @return array
     */
    public function validate(array $data): array
    {
        $errors = [];
        foreach ($data as $key => $value) {
            if ($key === 'client_name') {
                if (empty($value)) {
                    $errors[] = 'Имя не может быть пустым!';
                } elseif (strlen($value) < 2) {
                    $errors[] = 'Имя должно быть не менее 2 символов!';
                } elseif (strlen($value) > 20) {
                    $errors[] = 'Имя должно быть не более 20 символов!';
                }
            }
            if ($key === 'client_phone') {
                if (empty($value)) {
                    $errors[] = 'Телефон не может быть пустым!';
                } elseif (strlen($value) != 10) {
                    $errors[] = 'Телефон должен содержать 10 цифр!';
                } elseif (!ctype_digit($value)) {
                    $errors[] = 'Телефон должен содержать только цифры!';
                }
            }
        }

        return $errors;
    }
}