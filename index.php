<?php
require_once __DIR__ . '/vendor/autoload.php';

use Elisad5791\Phpapp\EmailVerifier;
use Elisad5791\Phpapp\DefaultMxChecker;

$mxChecker = new DefaultMxChecker();
$verifier = new EmailVerifier($mxChecker);

$emails = [
    'valid.email@example.com',
    'invalid.email@',
    'another.email@nonexistentdomain.xyz',
    'another.email@aaasssddd.ggg',
    'test@google.com',
    'wrong-format'
];
$results = $verifier->verifyEmails($emails);

foreach ($results as $email => $result) {
    echo "Email: $email\n";
    echo "Valid format: " . ($result['format_is_valid'] ? 'Yes' : 'No') . "\n";
    echo "Valid MX: " . ($result['mx_is_valid'] ? 'Yes' : 'No') . "\n";
    echo "Overall valid: " . ($result['valid'] ? 'Yes' : 'No') . "\n";
    echo "-----------------\n";
}