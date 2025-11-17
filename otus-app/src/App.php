<?php

declare(strict_types=1);

namespace App;

use App\Dto\EmailValidateEntryDto;
use App\Handler\EmailValidateHandler;
use App\Service\EmailValidationService;
use App\Service\RequestBodyService;
use Throwable;

class App
{
    public function handleRequest(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->handleResponse('Invalid request method', 400);
        }

        try {
            $decodedRequestBody = RequestBodyService::getDecodedJsonBody();

            $entryDto = new EmailValidateEntryDto($decodedRequestBody['emailList']);
            $isValidEmailList = (new EmailValidateHandler(new EmailValidationService()))->handle($entryDto);

            return $isValidEmailList
                ? $this->handleResponse('Email list is valid')
                : $this->handleResponse('Email list is not valid', 400);
        } catch (Throwable) {
            return $this->handleResponse('Invalid request body', 400);
        }
    }

    private function handleResponse(string $message, int $statusCode = 200): string {
        http_response_code($statusCode);

        return $message;
    }
}
