<?php

namespace Larkinov\Myapp\Services;

use Exception;
use Larkinov\Myapp\Classes\Email;

class EmailValidation
{

    private const PATTERN = '/^[a-z0-9]{2,64}@[a-z][a-z0-9]{1,62}\.[a-z]{2,63}$/i';

    public function __construct(private Email $email) {}

    public function validate(): bool
    {
        if (strlen($this->email->getEmail()) > 254)
            throw new Exception('email is too long');

        $result = preg_match(self::PATTERN, $this->email->getEmail());

        if ($result === false || $result === 0)
            throw new Exception('no valid email');

        $emailHosts = [];

        getmxrr(explode('@', $this->email->getEmail())[1], $emailHosts);

        if(empty($emailHosts))
            throw new Exception('no hosts found for the mail domain');

        return true;
    }
}
