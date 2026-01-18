<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Interfaces\ValidateEmailsUseCaseInterface;
use App\Domain\DTO\EmailValidationRequest;
use App\Domain\DTO\EmailValidationResult;
use App\Domain\Interfaces\EmailValidatorInterface;

class ValidateEmailsUseCase implements ValidateEmailsUseCaseInterface
{
    public function __construct(
        private readonly EmailValidatorInterface $emailValidator
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
            $results[] = new EmailValidationResult($email, $isValid);
        }

        return $results;
    }
}
