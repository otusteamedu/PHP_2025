<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Alisaselezneva\Code\Domain\Services\EmailVerifier;

$verifier = new EmailVerifier();

$emails = [
    "valid@example.com",
    "invalid-email",
    "test@1234567890.com",
    "test@1234567890.123",
    "another@test..org",
    ".a@test.org",
    "a.@test.org",
    "invalid@testorg",
    "invalid@.test.org",
    "invalid@test.org.",
    "VALID-EMAIL-1234567890_test@test.org",
    "inva..lid@test.org",
    "invalid#test.org",
];

$results = $verifier->massVerify($emails);

foreach ($results as $index => $result) {
    echo "Email: " . $emails[$index] . " - " . 
         ($result->isValid() ? "VALID." : "INVALID.") .
         " Checks: " . json_encode($result->getChecks()) . "\n\n";
}