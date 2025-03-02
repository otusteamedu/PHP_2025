<?php declare(strict_types=1);

namespace App\Validations;

use App\Exceptions\ValidationException;

class EmailValidation
{
    private const string ERROR_REQUIRED = 'обязательный параметр';
    private const string ERROR_NOT_STRING = 'не является строкой';
    private const string ERROR_INVALID_EMAIL = 'не является email адресом';
    private const string ERROR_NON_EXISTENT_EMAIL = 'не существующий email адрес';

    /**
     * Проверяет строку на наличие корректных email-адресов.
     *
     * @param array $post
     * @param string $key
     * @return array
     * @throws ValidationException
     */
    public function validate(array $post, string $key): array
    {
        if (empty($post[$key])) {
            throw new ValidationException("Validation error", [
                $key => self::ERROR_REQUIRED
            ]);
        }

        $emails = $post[$key];
        $emails = is_array($emails) ? $emails : [$emails];

        $this->validateEmails($emails);

        return $emails;
    }

    /**
     * Проверяет корректность введенных emails.
     *
     * @param array $emails
     * @throws ValidationException
     */
    private function validateEmails(array $emails): void
    {
        $errors = [];

        foreach ($emails as $key => $email) {
            if (!is_string($email)) {
                $errors["emails.$key"] = self::ERROR_NOT_STRING;
                continue;
            }

            if (!$this->isValidEmailFormat($email)) {
                $errors["emails.$key"] = "$email " . self::ERROR_INVALID_EMAIL;
                continue;
            }

            if (!$this->isValidEmailDomain($email)) {
                $errors["emails.$key"] = "$email " . self::ERROR_NON_EXISTENT_EMAIL;
            }
        }

        if (!empty($errors)) {
            throw new ValidationException("Validation error", $errors);
        }
    }

    /**
     * Проверяет формат email.
     *
     * @param string $email
     * @return bool
     */
    private function isValidEmailFormat(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Проверяет, существует ли домен email.
     *
     * @param string $email
     * @return bool
     */
    private function isValidEmailDomain(string $email): bool
    {
        $domain = substr(strrchr($email, "@"), 1);
        return checkdnsrr($domain);
    }
}