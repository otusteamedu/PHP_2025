<?php

declare(strict_types=1);

const OPEN_TAG = '(';
const CLOSE_TAG = ')';

$string = $_REQUEST['string'] ?? '';
$err = false;

//проверить строку на пустоту и парность
if (mb_strlen($string, 'UTF-8') < 1 || mb_strlen($string, 'UTF-8') % 2 !== 0) {
    $err = true;
} else {
//проверить строку на соответствие порядку символов
    $par = 0;
    for ($i = 0; $i < mb_strlen($string); $i++) {
        if ($string[$i] === OPEN_TAG) {
            ++$par;
        } elseif ($string[$i] === CLOSE_TAG) {
            --$par;
        }

        if ($par < 0) {
            $err = true;
            break;
        }
    }
}

if ($err) {
    $header_string = "HTTP/1.1 400 Bad Request";
    $return_code = 400;
} else {
    $header_string = "HTTP/1.1 200 Ok";
    $return_code = 200;
}

header($header_string, true, $return_code);
