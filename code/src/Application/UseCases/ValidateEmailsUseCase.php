<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Interfaces\ValidateEmailsUseCaseInterface;
use App\Domain\DTO\EmailValidationRequest;
use App\Domain\DTO\EmailValidationResult;
use App\Domain\Validators\EmailValidator;

class ValidateEmailsUseCase implements ValidateEmailsUseCaseInterface
{
    public function __construct(
        private readonly EmailValidator $emailValidator
    ) {}

    /**
     * @param EmailValidationRequest $request
     * @return EmailValidationResult[]
     */
    public function execute(EmailValidationRequest $request): array
    {
        $results = [];

        foreach ($request->emails as $email) {
            $isValid = $this->emailValidator->validate($email);
            $error = $this->emailValidator->getError();
            $results[] = new EmailValidationResult($email, $isValid, $error);
        }

        return $results;
    }
}
