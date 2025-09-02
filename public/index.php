<?php

declare(strict_types=1);

use Dinargab\Homework5\Service\EmailValidator;

require '../vendor/autoload.php';


$emailValidator = new EmailValidator;

var_dump($emailValidator->verifyEmail("dinar@reaspekt.ru")->isValid());


$emailsToCheck = [
    'help@otus.ru',
    'test@example.com',
    'test',
    'invalid.email',
    'no-mx@nomxdomain.org',
    'test@..gmail.com',
];

echo $validationResults = $emailValidator->verifyEmails($emailsToCheck, true);

