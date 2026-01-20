<?php

return [
    'driver' => 'sqlite',
    'database' => $_ENV['DB_PATH'] ?? __DIR__ . '/../data/bot_database.sqlite',
    'prefix' => '',
];