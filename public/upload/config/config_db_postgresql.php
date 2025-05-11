<?php
declare(strict_types=1);

return [
    'HOST' => getenv('POSTGRES_CONTAINER'),
    'PORT' => getenv('POSTGRES_PORT'),
    'LOGIN' => getenv('POSTGRES_USER'),
    'PASSWORD' => getenv('POSTGRES_PASSWORD'),
    'DATABASE' => getenv('POSTGRES_DATABASE'),
];
