<?php
declare(strict_types=1);

return [
    'HOST' => getenv('MYSQL_CONTAINER'),
    'PORT' => getenv('MYSQL_PORT'),
    'LOGIN' => getenv('MYSQL_USER'),
    'PASSWORD' => getenv('MYSQL_PASSWORD'),
    'DATABASE' => getenv('MYSQL_DATABASE'),
];
