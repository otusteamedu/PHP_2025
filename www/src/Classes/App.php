<?php

namespace Larkinov\Myapp\Classes;

use Larkinov\Myapp\Services\EmailValidation;

class App
{
    public function run():bool
    {
        auth();

        $email = new Email($_POST['email']);

        return (new EmailValidation($email))->validate();
    }
}
