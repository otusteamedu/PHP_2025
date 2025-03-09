<?php

namespace App\Validation;

use App\Contracts\RequestPipeInterface;
use App\Exceptions\EmailValidationException;
use App\Http\Request;

class EmailValidation implements RequestPipeInterface
{
    private const string NOT_A_STRING = 'не является строкой';
    private const string INVALID_EMAIL = 'не является email адресом';
    private const string EMAIL_NOT_EXISTS = 'email адрес не существует';

    /**
     * @param Request $request
     * @param $next
     * @return Request
     * @throws EmailValidationException
     */
    public function validate(Request $request, $next): Request
    {
        $this->validateEmails($request->get('emails'));

        return $next($request);
    }

    /**
     * @param array $emails
     * @return void
     * @throws EmailValidationException
     */
    private function validateEmails(array $emails): void
    {
        $errors = [];

        foreach ($emails as $key => $email) {
            if (!is_string($email)) {
                $errors["emails.$key"] = self::NOT_A_STRING;
                continue;
            }

            if (!$this->isValidEmailFormat($email)) {
                $errors["emails.$key"] = "$email " . self::INVALID_EMAIL;
                continue;
            }

            if (!$this->isValidEmailDomain($email)) {
                $errors["emails.$key"] = "$email " . self::EMAIL_NOT_EXISTS;
            }
        }

        if (!empty($errors)) {
            throw new EmailValidationException('', $errors);
        }
    }

    /**
     * @param string $email
     * @return bool
     */
    private function isValidEmailFormat(string $email): bool
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param string $email
     * @return bool
     */
    private function isValidEmailDomain(string $email): bool
    {
        return checkdnsrr(substr(strrchr($email, "@"), 1));
    }
}