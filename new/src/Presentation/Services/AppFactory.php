<?php

namespace EmailsVerifier\Presentation\Services;

use EmailsVerifier\Infrastructure\ValidationStrategyFactory;
use EmailsVerifier\Application\UseCases\VerifyEmailsUseCase;
use EmailsVerifier\Presentation\Console\ConsoleRunner;
use EmailsVerifier\Presentation\Controllers\EmailVerificationController;

class AppFactory
{
    public static function createConsoleRunner(): ConsoleRunner
    {
            $validationStrategy = ValidationStrategyFactory::createStrategy();
        $verifyEmailsUseCase = new VerifyEmailsUseCase($validationStrategy);
        return new ConsoleRunner($verifyEmailsUseCase);
    }

    public static function createEmailVerificationController(): EmailVerificationController
    {
        $validationStrategy = ValidationStrategyFactory::createStrategy();
        $verifyEmailsUseCase = new VerifyEmailsUseCase($validationStrategy);
        return new EmailVerificationController($verifyEmailsUseCase);
    }
}
