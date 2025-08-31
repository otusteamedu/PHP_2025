<?php

declare(strict_types=1);

use Dinargab\Homework5\Service\EmailValidator;

require '../vendor/autoload.php';


$emailValidator = new EmailValidator;

var_dump($emailValidator->verifyEmail("dinar@reaspekt.ru")->isValid());


$emailsToCheck = [
    'dinar@reaspekt.ru',
    'help@otus.ru',
    'test@example.com',
    'тест@проверка.рф',
    'mailbox@domrf.ru',
    'test@дом.рф',
    '.test.ru',
    '123@otus.ru',
    '221@222@example.com',
    'mymail@yourdomain.com'
];

$validationResults = $emailValidator->verifyEmails($emailsToCheck);
foreach ($validationResults as $result) {
    echo $result->getEmail() . "is " . ($result->isValid() ? "valid" : "invalid");
    if (!$result->isValid()) {
        echo "Error" . $result->getError();
    }
}