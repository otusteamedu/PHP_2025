<?php
require_once __DIR__ . '/vendor/autoload.php';

use Elisad5791\Phpapp\EmailVerifier;

$verifier = new EmailVerifier();

$emails = [
    'valid.email@example.com',
    'invalid.email@',
    'another.email@nonexistentdomain.xyz',
    'another.email@aaasssddd.ggg',
    'test@google.com',
    'wrong-format'
];

$result = $verifier->verifyEmails($emails);
echo $result;