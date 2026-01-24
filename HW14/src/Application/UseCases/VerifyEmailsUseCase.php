<?php
declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTO\EmailsInputDTO;
use App\Application\DTO\EmailsOutputDTO;
use App\Application\DTO\EmailValidationInfoDTO;
use App\Domain\Interfaces\EmailValidatorInterface;

final class VerifyEmailsUseCase
{
    private EmailValidatorInterface $emailValidator;

    public function __construct(EmailValidatorInterface $emailValidator)
    {
        $this->emailValidator = $emailValidator;
    }

    /**
     * @param EmailsInputDTO $emailsInputDTO
     * @return EmailsOutputDTO
     */
    public function execute(EmailsInputDTO $emailsInputDTO): EmailsOutputDTO
    {
        $validationResults = [];

        foreach ($emailsInputDTO->emails as $email) {
            $isValid = $this->emailValidator->validate($email);
            $errors = $this->emailValidator->getErrors();

            $validationResults[] =  new EmailValidationInfoDTO(
                $email,
                $isValid,
                $errors
            );
        }

        return new EmailsOutputDTO($validationResults);
    }
}
