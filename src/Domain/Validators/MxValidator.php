<?php

namespace EmailsVerifier\Domain\Validators;

use EmailsVerifier\Domain\Email;
use EmailsVerifier\Domain\Interfaces\MxCheckerInterface;

readonly class MxValidator extends BaseValidator
{
    public function __construct(
        private MxCheckerInterface $mxChecker
    ) {}

    public function validate(Email $email): array
    {
        $errors = [];
        $address = $email->getAddress();
        $parts = explode('@', $address);

        if (count($parts) !== 2) {
            $errors[] = $this->addError('Некорректное количество символов @');
            return $errors;
        }

        $domain = $parts[1];
        if (empty($domain)) {
            $errors[] = $this->addError('Домен не может быть пустым');
            return $errors;
        }

        $result = $this->mxChecker->hasMxRecord($domain);

        if (!$result) {
            $errors[] = $this->addError('MX-запись не найдена');
        }

        return $errors;
    }
}
