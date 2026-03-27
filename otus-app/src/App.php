<?php

declare(strict_types=1);

namespace App;

use App\Exception\CustomException;
use App\Handler\CheckEmailList;
use App\Service\RequestBodyService;
use Exception;
use Throwable;

class App
{
    public function handleRequest(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->handleResponse('Invalid request method', 400);
        }

        try {
            $requestBody = new RequestBodyService();

            try {
                $decodedRequestBody = $requestBody->getDecodedJsonBody();
            } catch (Exception) {
                return $this->handleResponse('Invalid request body', 400);
            }

            $handler = new CheckEmailList();

            try {
                $response = $handler->process($decodedRequestBody);
            } catch (CustomException $e) {
                return $this->handleResponse($e->getMessage(), $e->getCode());
            }

            return $this->handleResponse($response);
        } catch (Throwable) {
            return $this->handleResponse('Invalid request body', 400);
        }
    }

    private function handleResponse(string $message, int $statusCode = 200): string {
        http_response_code($statusCode);

        return $message;
    }
}
