<?php

declare(strict_types=1);

namespace App;

use Throwable;

class App
{
    public function handleRequest(): void
    {
        try {
            $requestBody = new RequestBodyService();
            $service = new EmailChecker();

            $decodedRequestBody = $requestBody->getDecodedJsonBody();
            $emailList = $decodedRequestBody['emailList'];

            $isValidEmailList = $service->isValidEmailList($emailList);

            $isValidEmailList
                ? $this->handleResponse('Email list is valid')
                : $this->handleResponse('Email list is not valid', 400);
        } catch (Throwable) {
            $this->handleResponse('Invalid request body', 400);
        }
    }

    private function handleResponse(string $message, int $statusCode = 200): void {
        http_response_code($statusCode);
        echo $message;
    }
}
