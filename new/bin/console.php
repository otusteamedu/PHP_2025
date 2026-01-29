<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EmailsVerifier\Infrastructure\ValidationStrategyFactory;
use EmailsVerifier\Application\UseCases\VerifyEmailsUseCase;
use EmailsVerifier\Presentation\Console\ConsoleRunner;

// --- DI
$validationStrategy = ValidationStrategyFactory::createStrategy();
$verifyEmailsUseCase = new VerifyEmailsUseCase($validationStrategy);
$consolePresenter = new ConsoleRunner($verifyEmailsUseCase);
// ---

$consolePresenter->verifyEmailsFromArgs($argv);
