<?php
require_once 'classes/EmailValidator.php';


$arEmails = [
    'pavel.klimenko.1989@gmail.com',
    'pavel89@rambler.ru',
    'pavel.klimenko.89@yandex.by',
    'testMail',
    'testMail@eeeeeee.su',
];

if (!is_array($arEmails)) throw new RuntimeException('$arEmails is not an array');
if (empty($arEmails)) throw new RuntimeException('There is no list of emails');


$emailValidator = new \classes\EmailValidator();

$arResult = [];
foreach ($arEmails as $email) {
    $arResult[$email]['IS_FORMAT_VALID'] = $emailValidator->validateEmailFormat($email);
    $arResult[$email]['ARE_DNS_MX_EXIST'] = $emailValidator->checkEmailByDnsMX($email);
}

echo '<pre>';
var_dump($arResult);
echo '</pre>';