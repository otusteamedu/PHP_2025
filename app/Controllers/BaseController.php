<?php

namespace App\Controllers;

use JetBrains\PhpStorm\NoReturn;
use RuntimeException;

abstract class BaseController
{
    /**
     * @return array
     */
    protected function getUploadedFile(): array
    {
        if (!isset($_FILES['file'])) {
            throw new RuntimeException('No file uploaded');
        }

        return [
            'name' => $_FILES['file']['name'],
            'tmp' => $_FILES['file']['tmp_name'],
            'size' => $_FILES['file']['size'],
            'directory_name' => $_POST['directory_name'] ?? null
        ];
    }

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
        if (!isset($_POST['directory_name'])) {
            throw new RuntimeException('No directory name');
        }

        return [
            'directory_name' => $_POST['directory_name']
        ];
    }
}