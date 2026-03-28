<?php

require __DIR__ . '/vendor/autoload.php';

use Hafiz\Php2025\Service\EmailVerificationService;

$emailsToVerify = [
    'valid@example.com',
    'invalid-email.com',
    'another@domain.com',
    'missing-mx@domainwithnomx.com',
];

$service = new EmailVerificationService();
$verificationResults = $service->verifyEmails($emailsToVerify);

echo "<pre>";
print_r($verificationResults);
echo "</pre>";
