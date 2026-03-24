<?php

declare(strict_types=1);

namespace App\Config;

return [
    'in' => new ServerConfig(
        host: '192.168.11.207',
        login: 'admin',
        password: '12345678!',
        name: 'Вход'
    ),

    'out' => new ServerConfig(
        host: '192.168.11.205',
        login: 'admin',
        password: '12345678!',
        name: 'Выход'
    ),
];