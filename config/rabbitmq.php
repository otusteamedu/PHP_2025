<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../', '.env.example');
$dotenv->load();

return [
    'host' => $_ENV['RABBITMQ_HOST'],
    'port' => $_ENV['RABBITMQ_PORT'],
    'user' => $_ENV['RABBITMQ_USER'],
    'password' => $_ENV['RABBITMQ_PASS'],
    'vhost' => $_ENV['RABBITMQ_VHOST'],
    'queue' => $_ENV['RABBITMQ_QUEUE'],
];

