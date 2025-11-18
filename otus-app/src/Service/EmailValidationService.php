<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\EmailValidateResultDto;
use App\Validator\EmailValidatorInterface;
use Exception;

class EmailValidationService
{
    /** @var EmailValidatorInterface[] */
    private array $validatorList = [];

    /**
     * @throws Exception
     */
    public function checkEmailList(array $emailList): EmailValidateResultDto
    {
        if (empty($emailList)) {
            return new EmailValidateResultDto();
        }

        $validEmailList = [];
        $invalidEmailList = [];

        foreach ($emailList as $email) {
            $isValidEmail = $this->isValidEmail($email);

            if ($isValidEmail === true) {
                $validEmailList[] = $email;
            } else {
                $invalidEmailList[] = $email;
            }
        }

        return new EmailValidateResultDto($validEmailList, $invalidEmailList, empty($invalidEmailList));
    }

    public function setValidator(EmailValidatorInterface $validator): void
    {
        $this->validatorList[] = $validator;
    }

    private function isValidEmail(mixed $email): bool
    {
        if (!is_string($email)) {
            return false;
        }

        foreach ($this->validatorList as $validator) {
            if ($validator->isValidEmail($email) === false) {
                return false;
            }
        }

        return true;
    }
}
