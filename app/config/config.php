<?php

return [
    'driver' => getenv('DB_DRIVER'),
    'dbname' => getenv('DB_NAME'),
    'host' => getenv('DB_CONTAINER_NAME'),
    'port' => getenv('DB_PORT'),
    'user' => getenv('DB_USER'),
    'pass' => getenv('DB_PASSWORD'),
];
