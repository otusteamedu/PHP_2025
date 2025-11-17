<?php

declare(strict_types=1);

namespace App\Service;

use App\Validator\EmailValidatorInterface;
use Exception;

class EmailValidationService
{
    /** @var EmailValidatorInterface[] */
    private array $validatorList = [];

    /**
     * @throws Exception
     */
    public function isValidEmailList(array $emailList): bool
    {
        if (empty($emailList)) {
            return false;
        }

        foreach ($emailList as $email) {
            if (!is_string($email)) {
                return false;
            }

            foreach ($this->validatorList as $validator) {
                if ($validator->isValidEmail($email) === false) {
                    return false;
                }
            }
        }

        return true;
    }

    public function setValidator(EmailValidatorInterface $validator): void
    {
        $this->validatorList[] = $validator;
    }
}
