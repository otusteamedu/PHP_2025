<?php

declare(strict_types=1);

namespace App\Controllers;

abstract class BaseController
{
    public array $errors = [];

    /**
     * @param $data
     * @param int $statusCode
     * @return false|string
     */
    protected function jsonResponse($data, int $statusCode = 200): false|string
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        return json_encode($data);
    }

    /**
     * @return array
     */
    public function getRequestData(): array
    {
        return [
            'client_name' => $_POST['client_name'] ?? null,
            'client_phone' => $_POST['client_phone'] ?? null,
            'product_id' => $_POST['product_id'] ?? null,
            'ingredients' => $_POST['ingredients'] ?? null,
            'payment' => $_POST['payment'] ?? null,
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function validate(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($key === 'client_name') {
                if (empty($value)) {
                    $this->errors[] = 'Имя не может быть пустым!';
                } elseif (strlen($value) < 2) {
                    $this->errors[] = 'Имя должно быть не менее 2 символов!';
                } elseif (strlen($value) > 20) {
                    $this->errors[] = 'Имя должно быть не более 20 символов!';
                }
            }
            if ($key === 'client_phone') {
                if (empty($value)) {
                    $this->errors[] = 'Телефон не может быть пустым!';
                } elseif (strlen($value) != 10) {
                    $this->errors[] = 'Телефон должен содержать 10 цифр!';
                } elseif (!ctype_digit($value)) {
                    $this->errors[] = 'Телефон должен содержать только цифры!';
                }
            }
        }

        return $this->errors;
    }
}