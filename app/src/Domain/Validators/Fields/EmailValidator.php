<?php
namespace Pryaniki\App\Domain\Validators\Fields;

use Pryaniki\App\Exceptions;
use Pryaniki\App\Exceptions\EmptyFieldException;
use Pryaniki\App\Exceptions\NotExistDomain;
use Pryaniki\App\Exceptions\NotValidException;
use Pryaniki\App\Domain\Validators\BaseValidator;

class EmailValidator extends BaseValidator
{
    private string $validationError = '';

    public function validate(mixed $value, string $fieldName): bool
    {
        $isValid = false;

        try {
            self::checkEmail($value, $fieldName);
            self::checkDns();
            $isValid = true;
        } catch (\Exception $e) {
            $this->validationError = $e->getMessage() . PHP_EOL;
        }

        return $isValid;
    }

    /**
     * @throws NotValidException
     * @throws EmptyFieldException
     */
    private function checkEmail(string $email, string $fieldName): void
    {
        if ($email === '') {
            throw new Exceptions\EmptyFieldException($fieldName);
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new Exceptions\NotValidException($fieldName);
        }
    }

    /**
     * @throws NotExistDomain
     */
    private function checkDns(): void
    {
        if (!$this->isExistsDomain()) {
            throw new Exceptions\NotExistDomain('DNS record not found');
        }
    }

    private function isExistsDomain(): bool
    {
        return checkdnsrr(self::getDomainFromEmail(), 'MX');
    }

    private function getDomainFromEmail(): string
    {
        return array_last(explode('@', $this->email));
    }

    public function getValidationError(): string
    {
        return $this->validationError;
    }
}