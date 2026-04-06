<?php
namespace Pryaniki\App\Application\UseCases;

use Pryaniki\App\Application\DTO\ValidateEmailRequestDTO;
use Pryaniki\App\Domain\Models\Email;
use Pryaniki\App\Application\DTO\ValidationEmailResponseDTO;
use Pryaniki\App\Domain\ValueObjects\Email\CompositeStringValidator;
use Pryaniki\App\Domain\ValueObjects\Email\Rules\EmailFormatRule;
use Pryaniki\App\Domain\ValueObjects\Email\Rules\NotEmptyRule;
use Pryaniki\App\Infrastructure\Services\DnsDomainChecker;


class ValidateEmailUseCase
{
    private string $email;

    /**
     * @param ValidateEmailRequestDTO $dto
     */
    public function __construct(ValidateEmailRequestDTO $dto)
    {
        $this->email = $dto->email;
    }

    public function execute(string $email): ValidationEmailResponseDTO
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

        return new ValidationEmailResponseDTO($validationResult->isValid(),
            $validationResult->getErrors()
        );
    }
}