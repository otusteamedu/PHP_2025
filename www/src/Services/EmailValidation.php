<?php

namespace Larkinov\Myapp\Services;

use Larkinov\Myapp\Exceptions\Email\ExceptionEmailHost;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmailParameters;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmailValidation;

class EmailValidation
{

    private const PATTERN = '/^[a-z0-9.\-_]{2,64}@[a-z][a-z0-9]{1,62}\.[a-z]{2,63}$/i';

    public function isValid(): void
    {
        if (!isset($_POST['email']))
            throw new ExceptionEmailParameters('not found argument');

        if (empty($_POST['email']))
            throw new ExceptionEmailParameters('empty argument');

        if (strlen($_POST['email']) > 254)
            throw new ExceptionEmailParameters('email is too long');

        $result = preg_match(self::PATTERN, $_POST['email']);

        if ($result === false || $result === 0)
            throw new ExceptionEmailValidation('no valid email');

        $emailHosts = [];

        getmxrr(explode('@', $_POST['email'])[1], $emailHosts);

        if (empty($emailHosts))
            throw new ExceptionEmailHost('no hosts found for the mail domain');
    }
}
