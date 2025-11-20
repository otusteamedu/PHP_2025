<?php

declare(strict_types=1);

namespace Otus\Services;

use Otus\Contracts\EmailValidatorServiceInterface;

class EmailValidatorService implements EmailValidatorServiceInterface
{
    /**
     * @param string $email
     *
     * @return bool
     */
    public function handle(string $email): bool
    {
        return $this->filter($email) && $this->dns($email);
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    protected function dns(string $email): bool
    {
        [
            ,
            $domain,
        ] = explode('@', $email);

        return checkdnsrr($domain);
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    protected function filter(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) === $email;
    }
}
