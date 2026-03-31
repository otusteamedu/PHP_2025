<?php

declare(strict_types=1);

namespace AppV2;

use Exception;
use Throwable;

class EmailChecker //нужно вынести в отдельную директорию
{
    /**
     * @throws Exception
     */
    public function isValidEmailList(array $emailList): bool
    {
        if (empty($emailList)) {
            return false;
        }

        foreach ($emailList as $email) {
            if (!is_string($email)) {
                return false;
            }

            if ($this->isValidEmail($email) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function isValidEmail(string $email): bool
    {
        //при добавлении новой проверки, придется менять этот метод. + нарушен принцип единственной ответственности, тк один метод делает разные вариации проверок: проверяет dns, отправляет запрос на подтверждение и тп
        if (empty(trim($email))) {
            return false;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        $domain = explode('@', $email)[1];

        if (checkdnsrr($domain) === false) {
            return false;
        }

        if ($this->isEmailConfirmationSent() === false) {//данный метод может обращаться к стороннему сервису для отправки емейла
            return false;
        }

        return true;
    }

    private function isEmailConfirmationSent(): bool
    {
        try {
            //some logics
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
