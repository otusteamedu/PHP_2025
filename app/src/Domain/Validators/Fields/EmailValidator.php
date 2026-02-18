<?php
namespace Pryaniki\App\Domain\Validators\Fields;

use Pryaniki\App\Exceptions;
use Pryaniki\App\Exceptions\EmptyFieldException;
use Pryaniki\App\Exceptions\NotExistDomain;
use Pryaniki\App\Exceptions\NotValidException;

use Pryaniki\App\Domain\Interfaces\ValidatorInterface;

class EmailValidator implements ValidatorInterface
{
    private string $email;

    private string $validationError = '';

    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function validate(): bool
    {
        $isValid = false;

        try {
            self::checkEmail();
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
    private function checkEmail(): void
    {
        if ($this->email === '') {
            throw new Exceptions\EmptyFieldException('email');
        }

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            throw new Exceptions\NotValidException('email');
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