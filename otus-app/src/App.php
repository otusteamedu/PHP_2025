<?php

declare(strict_types=1);

namespace App;

use App\Dto\EmailValidateEntryDto;
use App\Dto\ResponseDto;
use App\Handler\EmailValidateHandler;
use App\Service\EmailValidationService;
use App\Service\RequestBodyService;
use Throwable;

class App
{
    public function handleRequest(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->handleResponse(new ResponseDto('Invalid request method', code: 400));
        }

        try {
            $decodedRequestBody = RequestBodyService::getDecodedJsonBody();

            $entryDto = new EmailValidateEntryDto($decodedRequestBody['emailList']);
            $responseDto = (new EmailValidateHandler(new EmailValidationService()))->handle($entryDto);

            return $this->handleResponse($responseDto);
        } catch (Throwable) {
            return $this->handleResponse(new ResponseDto('Invalid request body', code: 400));
        }
    }

    private function handleResponse(ResponseDto $responseDto): string {
        http_response_code($responseDto->code);

        return $responseDto->getResponseMessage();
    }
}
