<?php
namespace Pryaniki\App\Domain\Validators\Fields;

use Pryaniki\App\Exceptions\Validation\Field\EmptyFieldException;
use Pryaniki\App\Exceptions\Validation\Field\FieldException;
use Pryaniki\App\Exceptions\Validation\Field\NotValidException;
use Pryaniki\App\Domain\Validators\BaseValidator;

class EmailValidator extends BaseValidator
{
    private string $validationError = '';

    public function validate(mixed $value, string $fieldName): bool
    {
        $isValid = false;

        try {
            self::checkEmail($value, $fieldName);
            $isValid = true;
        } catch (FieldException $e) {
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
            throw new EmptyFieldException($fieldName);
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new NotValidException($fieldName);
        }
    }

    public function getValidationError(): string
    {
        return $this->validationError;
    }
}