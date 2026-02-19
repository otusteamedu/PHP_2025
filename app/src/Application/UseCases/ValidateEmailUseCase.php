<?php
namespace Pryaniki\App\Application\UseCases;

use Pryaniki\App\Domain\Validators\Fields\EmailValidator;
use Pryaniki\App\Application\DTO\ValidationResultDTO;

class ValidateEmailUseCase
{
    public function execute(string $email): ValidationResultDTO
    {
        $emailValidator = new EmailValidator($email);
        $isValidEmail = $emailValidator->validate();
        $validationError = $emailValidator->getValidationError();

        return new ValidationResultDTO($isValidEmail,
            $validationError
        );
    }
}