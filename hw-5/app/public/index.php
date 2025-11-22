<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$emailsFilePath = dirname(__DIR__) . '/resources/emails.txt';
$checkedEmailsFilePath = dirname(__DIR__) . '/resources/checked_emails.txt';

$dnsEmailVerifier = new App\Infrastructure\DnsEmailVerifier();
$emailsFileReader = new App\Infrastructure\EmailsFileReader($emailsFilePath);
$checkedEmailsFileWriter = new App\Infrastructure\CheckedEmailsFileWriter($checkedEmailsFilePath);
$verificationEmailService = new App\Application\VerificationEmailService($checkedEmailsFileWriter, $emailsFileReader, $dnsEmailVerifier);
$verificationEmailCommand = new App\UserInterface\VerificationEmailCommand($verificationEmailService);

$verificationEmailCommand->handle();
