<?php

function validate_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Некорректный Email";
    }

    $domain = substr(strrchr($email, "@"), 1);
    return checkdnsrr($domain, "MX") ? "Корректный Email" : "Некорректный Email";
}

$emails = file("emails.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$results = [];

foreach ($emails as $email) {
    $results[$email] = validate_email($email);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
