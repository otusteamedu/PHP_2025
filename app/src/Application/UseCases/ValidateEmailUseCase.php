<?php
namespace Pryaniki\App\Application\UseCases;

use Pryaniki\App\Domain\Validators\Fields\EmailValidator;

class ValidateEmailUseCase
{
    public function execute(string $email): string
    {
        $emailValidator = new EmailValidator($email);
        $isValidEmail = $emailValidator->validate();

        if (!$isValidEmail) {
            return "Email $email is not valid: {$emailValidator->getValidationError()}" . PHP_EOL;
        }

        return "Email $email is valid" . PHP_EOL;
    }
}