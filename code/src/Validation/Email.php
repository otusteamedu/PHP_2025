<?php

namespace Ak\Hw\Validation;

final class Email
{
    public function __construct(
        protected $email = '',
    )
    {
    }

    /**
     * Проверяет адрес электронной почты, используя несколько проверок.
     *
     * @param bool $checkDns Выполнять ли проверку записи DNS MX.
     * @return bool True, если email действителен, false в противном случае.
     */
    public function isValid(string $email, bool $checkRegexp = false, bool $checkDns = false): bool
    {
        $this->email = $email;

        if (!$this->checkByFilter()) {
            return false;
        }

        if (!$this->checkByRegexp()) {
            return false;
        }

        if ($checkDns) {
            return $this->checkByDnsMx();
        }

        return true;
    }

    /**
     * Проверяет email с помощью функции filter_var PHP.
     *
     * @return bool
     */
    private function checkByFilter(): bool
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Проверяет email с помощью регулярного выражения.
     *
     * @return bool
     */
    private function checkByRegexp(): bool
    {
        $pattern = '/^(?!(?:(?:\x22?\x5C[\x00-\x7f]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7f]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7f]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7f]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
        return preg_match($pattern, $this->email) === 1;
    }

    /**
     * Проверяет наличие записей DNS MX для домена электронной почты.
     *
     * @return bool
     */
    private function checkByDnsMx(): bool
    {
        $domain = substr(strrchr($this->email, "@"), 1);
        if ($domain === false) {
            return false;
        }

        return checkdnsrr($domain, 'MX');
    }
}