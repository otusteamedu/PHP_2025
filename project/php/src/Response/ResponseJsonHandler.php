<?php

namespace App\Response;

use App\Interface\ResponseHandlerInterface;

class ResponseJsonHandler implements ResponseHandlerInterface {
    public function sendResponse(int $statusCode, array $data): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        header('X-Container-Name: ' . gethostname());
        echo json_encode($data);
    }
}