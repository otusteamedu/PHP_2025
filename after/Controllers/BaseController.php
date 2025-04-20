<?php

namespace Controllers;

abstract class BaseController
{
    /**
     * @return array
     */
    protected function getUploadedFile(): array
    {
        if (!isset($_FILES['file'])) {
            throw new \RuntimeException('No file uploaded');
        }

        return [
            'name' => $_FILES['file']['name'],
            'tmp' => $_FILES['file']['tmp_name'],
            'size' => $_FILES['file']['size']
        ];
    }

    /**
     * @param $data
     * @param int $statusCode
     * @return void
     */
    protected function jsonResponse($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}