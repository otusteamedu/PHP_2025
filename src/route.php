<?php

use Blarkinov\RedisCourse\Controllers\EventController;

return [
    [
        'method'  => 'POST',
        'pattern' => '/events',
        'handler' => fn() => (new EventController)->save(),

    ],
    [
        'method'  => 'GET',
        'pattern' => '/events',
        'handler' => fn() => (new EventController)->priority(),

    ],
    [
        'method'  => 'DELETE',
        'pattern' => '/events',
        'handler' => fn() => (new EventController)->destroy(),

    ],
];
