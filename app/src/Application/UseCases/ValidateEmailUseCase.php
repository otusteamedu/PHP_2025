<?php
namespace Pryaniki\App\Application\UseCases;

use Pryaniki\App\Domain\Interfaces\DomainExistenceCheckerInterface;
use Pryaniki\App\Domain\Models\Email;
use Pryaniki\App\Application\DTO\ValidationEmailResponseDTO;
use Pryaniki\App\Domain\ValueObjects\Email\EmailValidatorInterface;


readonly class ValidateEmailUseCase
{
    public function __construct(
        private EmailValidatorInterface         $validator,
        private DomainExistenceCheckerInterface $dnsChecker
    ) {
    }

    public function execute(string $email): ValidationEmailResponseDTO
    {
        $emailModel = new Email($email);

        $validationResult = $this->validator->validate($email);
         if (!$this->dnsChecker->isExistsDomain($emailModel->getDomain())) {
             $validationResult->setIsValid(false);
             $validationResult->addError('DNS record not found');
         }

        return new ValidationEmailResponseDTO($validationResult->isValid(),
            $validationResult->getErrors()
        );
    }
}