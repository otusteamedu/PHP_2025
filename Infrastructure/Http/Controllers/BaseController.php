<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

abstract class BaseController
{
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
            'product' => $_POST['product'] ?? null,
            'ingredients' => $_POST['ingredients'] ?? null,
            'payment' => $_POST['payment'] ?? null,
        ];
    }
}

