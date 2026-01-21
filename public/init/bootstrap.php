<?php

use Dotenv\Dotenv;

require __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable(realpath(__DIR__ . '/../../'));
$dotenv->load();
