<?php
namespace Pryaniki\App\Application\UseCases;

use Pryaniki\App\Domain\Models\Email;
use Pryaniki\App\Application\DTO\ValidationResultDTO;
use Pryaniki\App\Domain\ValueObjects\Email\Rules\CompositeStringValidator;
use Pryaniki\App\Domain\ValueObjects\Email\Rules\EmailFormatRule;
use Pryaniki\App\Domain\ValueObjects\Email\Rules\NotEmptyRule;
use Pryaniki\App\Infrastructure\Services\DnsDomainChecker;


class ValidateEmailUseCase
{
    public function execute(string $email): ValidationResultDTO
    {
        $emailModel = new Email($email);
        $validationRules = [
            new EmailFormatRule(),
            new NotEmptyRule()
        ];
        $emailValidator = new CompositeStringValidator($validationRules);

        $validationResult = $emailValidator->validate($email);
         if (!DnsDomainChecker::isExistsDomain($emailModel->getDomain())) {
             $validationResult->addError('DNS record not found');
         }

        return new ValidationResultDTO($validationResult->isValid(),
            $validationResult->getError()
        );
    }
}