<?php declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

// Init composer
require __DIR__ . '/../vendor/autoload.php';

// Init Symfony\Dotenv
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');