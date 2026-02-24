<?php

namespace EmailsVerifier\Presentation\Console;

use EmailsVerifier\Application\Interfaces\VerifyEmailsUseCaseInterface;
use EmailsVerifier\Presentation\Views\ResultPrinter;

readonly class ConsoleRunner
{
    public function __construct(
        private VerifyEmailsUseCaseInterface $verifyEmailsUseCase
    ) {
    }

    public function verifyEmailsFromArgs(array $args): void
    {
        array_shift($args);

        if (empty($args)) {
            echo "Ошибка: Не переданы email-адреса.\n";
            echo "Пример использования: php console.php email1 email2 email3...\n";
            exit(1);
        }

        $emails = $args;

        $results = $this->verifyEmailsUseCase->execute($emails);

        ResultPrinter::printResults($results);
    }
}
