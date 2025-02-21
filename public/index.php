<?php

$arEmails = [
  'pavel.klimenko.1989@gmail.com',
  'pavel89@rambler.ru',
  'pavel.klimenko.89@yandex.by',
  'testMail',
  'testMail@eeeeeee.su',
];

if (!is_array($arEmails)) throw new RuntimeException('$arEmails is not an array');
if (empty($arEmails)) throw new RuntimeException('There is no list of emails');

$arResult = [];
foreach ($arEmails as $email) {
    $arResult[$email]['IS_FORMAT_VALID'] = validateEmailFormat($email);
    $arResult[$email]['ARE_DNS_MX_EXIST'] = checkEmailByDnsMX($email);
}

echo '<pre>';
var_dump($arResult);
echo '</pre>';


function validateEmailFormat(string $email):bool
{
    return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
}

function checkEmailByDnsMX(string $email):bool
{
    $domain = substr(strrchr($email, "@"), 1);

    $isValid = true;
    $res = getmxrr($domain, $mx_records, $mx_weight);
    if (false == $res || 0 == count($mx_records) || (1 == count($mx_records) && ($mx_records[0] == null  || $mx_records[0] == "0.0.0.0" ) ) ){
        $isValid = false;
    }

    return $isValid;
}
