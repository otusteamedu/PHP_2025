<?php
return [
    'session' => [
        'handler' => 'redis',
        'path' => 'tcp://app-redis:6379',
    ],
];
