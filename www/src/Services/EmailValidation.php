<?php

namespace Larkinov\Myapp\Services;

use Exception;

class EmailValidation
{

    private const PATTERN = '/^[a-z0-9]{2,64}@[a-z][a-z0-9]{1,62}\.[a-z]{2,63}$/i';

    public function isValid(): void
    {
        if (!isset($_POST['email']))
            throw new Exception('not found argument');

        if (empty($_POST['email']))
            throw new Exception('empty argument');

        if (strlen($_POST['email']) > 254)
            throw new Exception('email is too long');

        $result = preg_match(self::PATTERN, $_POST['email']);

        if ($result === false || $result === 0)
            throw new Exception('no valid email');

        $emailHosts = [];

        getmxrr(explode('@', $_POST['email'])[1], $emailHosts);

        if (empty($emailHosts))
            throw new Exception('no hosts found for the mail domain');
    }
}
