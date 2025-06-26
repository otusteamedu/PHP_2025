<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Validator\EmailVerifier;

$verifier = new EmailVerifier();

// Список email для проверки
$emailsToCheck = [
    'test@example.com',
    'invalid.email',
    'missing@domain.nonexistenttld',
    'user@gmail.com',
    'another.user@yahoo.com',
    'no.mx.records@thisdomainsurelydoesnotexist12345.com'
];

// Проверяем email
$results = $verifier->verifyEmails($emailsToCheck);

var_dump($results);