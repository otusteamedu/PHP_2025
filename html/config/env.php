<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

if (file_exists(__DIR__ . '/../.env')) {
    new Dotenv()
        ->usePutenv()
        ->load(__DIR__ . '/../.env');
} else {
    echo 'Environment configuration file ".env" is missing.';
    exit(1);
}
