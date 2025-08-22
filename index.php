<?php

declare(strict_types=1);

use Dinargab\Homework5\Service\EmailValidator;

require './vendor/autoload.php';


$emailValidator = new EmailValidator;

echo $emailValidator->verifyEmail("dinar@reaspekt.ru");


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

var_dump($emailValidator->verifyEmails($emailsToCheck));
