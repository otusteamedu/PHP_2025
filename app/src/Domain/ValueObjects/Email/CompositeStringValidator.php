<?php

namespace Pryaniki\App\Domain\ValueObjects\Email;

use Pryaniki\App\Domain\ValueObjects\Email\Rules\EmailRuleInterface;

class CompositeStringValidator implements EmailValidatorInterface
{
    /** @var EmailRuleInterface[] $rules */
    private array $rules;
    protected ValidationResult $validationResult;

    /**
     * @param EmailRuleInterface[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
        $this->validationResult = new ValidationResult();
    }

    public function validate(string $email): ValidationResult
    {
        $this->checkRules($email);
        return $this->validationResult;
    }

    private function checkRules(string $email): void
    {
        $this->validationResult->setIsValid(true);

        foreach ($this->rules as $rule) {
            $validationResult = $rule->validate($email);
            $this->addError($validationResult);
        }
    }

    private function addError(ValidationResult $validationResult): void
    {
        if ($validationResult->isValid() === false) {
            $this->validationResult->setIsValid(false);
            $errors = $validationResult->getErrors();
            foreach ($errors as $error) {
                $this->validationResult->addError($error);
            }
        }
    }
}