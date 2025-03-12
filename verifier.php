<?php

declare(strict_types=1);

require __DIR__ . '/src/EmailVerifier.php';

use App\EmailVerifier;

function getEmails(array $argv): array
{
    if (!isset($argv[1])) {
        echo "Использование: php verifier.php email@example.com\n";
        exit(1);
    }

    return array_slice($argv, 1);
}

$verifier = new EmailVerifier();
$emails = getEmails($argv);

foreach ($emails as $email) {
    echo $verifier->verify($email) . PHP_EOL;
}