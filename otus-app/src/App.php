<?php

namespace App;

use App\Handler\CheckString;
use App\Handler\GetServerInfo;
use App\Service\RequestDataService;
use Exception;
use JsonException;

class App
{
    public function run(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $message = (new GetServerInfo())->getInfo();

            return $this->handleResponse($message);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->handleResponse('Method not allowed', 400);
        }

        try {
            $data = (new RequestDataService())->getJsonDecodedRequestData();
        } catch (JsonException) {
            return $this->handleResponse('Invalid request body', 400);
        }

        try {
            $message = (new CheckString())->process($data);
        } catch (Exception $e) {
            return $this->handleResponse($e->getMessage(), $e->getCode());
        }

        return $this->handleResponse($message);
    }

    public function handleResponse(string $message, int $statusCode = 200): string
    {
        http_response_code($statusCode);

        return $message;
    }
}
