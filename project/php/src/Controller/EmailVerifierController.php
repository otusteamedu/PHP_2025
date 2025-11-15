<?php

namespace App\Controller;

use App\Interface\EmailValidatorInterface;
use App\Interface\RequestValidatorInterface;
use App\Interface\ResponseHandlerInterface;

class EmailVerifierController {
    private RequestValidatorInterface $requestValidator;
    private EmailValidatorInterface $emailValidator;
    private ResponseHandlerInterface $responseHandler;

    public function __construct(
        RequestValidatorInterface $requestValidator,
        EmailValidatorInterface $emailValidator,
        ResponseHandlerInterface $responseHandler
    ) {
        $this->requestValidator = $requestValidator;
        $this->emailValidator = $emailValidator;
        $this->responseHandler = $responseHandler;
    }

    public function handleRequest(): void {
        try {
            $validationResult = $this->requestValidator->validateRequest();

            if ($validationResult['status'] !== 200) {
                $this->responseHandler->sendResponse(
                    $validationResult['status'],
                    ['error' => $validationResult['error']]
                );
                return;
            }

            $results = $this->emailValidator->verifyEmails($validationResult['emails']);
            $this->responseHandler->sendResponse(200, $results);

        } catch (\Exception $e) {
            $this->responseHandler->sendResponse(
                500,
                ['error' => 'Server error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')]
            );
        }
    }
}