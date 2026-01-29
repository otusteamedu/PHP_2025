<?php

require_once __DIR__ . '/vendor/autoload.php';

use EmailsVerifier\Infrastructure\ValidationStrategyFactory;
use EmailsVerifier\Application\UseCases\VerifyEmailsUseCase;
use EmailsVerifier\Presentation\Controllers\EmailVerificationController;
use EmailsVerifier\Presentation\Views\ResultPrinter;

// --- DI
$validationStrategy = ValidationStrategyFactory::createStrategy();
$verifyEmailsUseCase = new VerifyEmailsUseCase($validationStrategy);
$controller = new EmailVerificationController($verifyEmailsUseCase);
// ---

$emails = [
    // Валидные
    'user@example.com',
    'test.email@domain.org',
    'user+tag@example.net',
    'firstname.lastname@company.co.uk',
    'user123@test-domain.com',
    // Невалидные
    'invalid-email',
    '@invalid.com',
    'user@',
    'user..double.dot@example.com',
    'user@.com',
    '.user@domain.com',
    // Невалидные - MX
    'user@nonexistentdomain12345.com',
    'test@foolishdomain999.ru',
    'user@sub.domain.example.ru',
    'user_name@example.info',
];

$results = $controller->verifyEmails($emails)['results'];

ResultPrinter::printResults($results);
