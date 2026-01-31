<?php
namespace Pryaniki\App;

use Pryaniki\App\Exceptions;
use Pryaniki\App\Exceptions\EmptyFieldException;
use Pryaniki\App\Exceptions\NotValidException;

class EmailValidator
{
    private string $email;

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
            echo $e->getMessage() . PHP_EOL;
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
    private function checkDns(): void
    {

    }
}