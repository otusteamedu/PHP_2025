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
    'no-mx@example.org',
    'test@..gmail.com',
    'check@дом.рф',
];

$validationResults = $emailValidator->verifyEmails($emailsToCheck);
foreach ($validationResults as $result) {
    echo $result->getInputValue() . "is " . ($result->isValid() ? "valid" : "invalid") . "\n";
    if (!$result->isValid()) {
        echo "Error" . $result->getError();
    }
}