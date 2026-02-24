<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EmailsVerifier\Presentation\Services\AppFactory;

$consolePresenter = AppFactory::createConsoleRunner();

$consolePresenter->verifyEmailsFromArgs($argv);
