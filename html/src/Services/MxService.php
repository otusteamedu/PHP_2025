<?php

declare(strict_types=1);

namespace Otus\Services;

use Otus\Contracts\MxServiceInterface;

class MxService implements MxServiceInterface
{
    /**
     * @param string $email
     *
     * @return bool
     */
    public function validate(string $email): bool
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
