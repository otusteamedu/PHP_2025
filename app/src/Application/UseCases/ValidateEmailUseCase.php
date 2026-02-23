<?php
namespace Pryaniki\App\Application\UseCases;

use Pryaniki\App\Domain\Models\Email;
use Pryaniki\App\Domain\Validators\Fields\EmailValidator;
use Pryaniki\App\Application\DTO\ValidationResultDTO;
use Pryaniki\App\Infrastructure\Services\DnsDomainChecker;
use Pryaniki\App\Exceptions\NotExistDomainException;


class ValidateEmailUseCase
{
    /**
     * @throws NotExistDomainException
     */
    public function execute(string $email): ValidationResultDTO
    {
        $emailModel = new Email($email);

        $emailValidator = new EmailValidator();
        $isValidEmail = $emailValidator->validate($email, 'Email');
        $validationError = $emailValidator->getValidationError();

         if (!DnsDomainChecker::isExistsDomain($emailModel->getDomain())) {
             throw new NotExistDomainException('DNS record not found');
         }

        return new ValidationResultDTO($isValidEmail,
            $validationError
        );
    }
}