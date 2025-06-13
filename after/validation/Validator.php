<?php

namespace App\validation;

class Validator implements ValidatorInterface {
    /** @var EmailValidationRule[] */
    private array $rules;

    public function __construct(array $rules) {
        $this->rules = $rules;
    }

    public function isValid(string $email): bool {
        foreach ($this->rules as $rule) {
            if (!$rule->isValid($email)) {
                return false;
            }
        }
        return true;
    }
}
