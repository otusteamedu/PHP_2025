<?php

use SenishinAleksey\Validator\EmailValidator;

require_once __DIR__.'/vendor/autoload.php';

$email = 'email@example.com';

var_dump(EmailValidator::isValid($email));

$email = 'email@example';

var_dump(EmailValidator::isValid($email));
