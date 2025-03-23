<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

define('ELASTICSEARCH_URL', $_ENV['ELASTICSEARCH_URL']);
define('ELASTICSEARCH_USER', $_ENV['ELASTICSEARCH_USER']);
define('ELASTICSEARCH_PASS', $_ENV['ELASTICSEARCH_PASS']);
define('DEBUG_MODE', $_ENV['DEBUG_MODE'] === 'true');