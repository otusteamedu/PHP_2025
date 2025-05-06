<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$dotenv = Dotenv::createArrayBacked(__DIR__.'/../../');
$env = $dotenv->load();

return [
    'db_connection' => $env['DB_CONNECTION'],
    'host' => $env['DB_HOST'],
    'dbname' => $env['DB_DATABASE'],
    'username' => $env['DB_USERNAME'],
    'password' => $env['DB_PASSWORD'],
    'charset' => 'utf8mb4',
];