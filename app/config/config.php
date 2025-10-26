<?php

return [
    'postgresHost' => getenv('POSTGRES_CONTAINER'),
    'postgresPort' => getenv('POSTGRES_PORT'),
    'postgresUsername' => getenv('POSTGRES_USER'),
    'postgresPassword' => getenv('POSTGRES_PASSWORD'),
    'postgresDbName' => getenv('POSTGRES_DB_NAME'),
];
