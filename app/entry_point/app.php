<?php

use App\Presentation\AppRunner;

require_once __DIR__  . '/../vendor/autoload.php';

$order = [
    'burger' =>  [
        [
            'type' => 'classic',
            'count' => 1,
            'additional_ingredients' => [
                'onion' => 1
            ]
        ]
    ],
    'pizza' => [
        [
            'type' => 'classic',
            'count' => 1,
            'additional_ingredients' => [
                'cheese' => 1
            ]
        ]
    ],
    'hot-dog' => [
        [
            'type' => 'classic',
            'count' => 2,
            'additional_ingredients' => [
                'fried-onions' => 1
            ]
        ]
    ],
    'sandwich' => [
        [
            'type' => 'classic',
            'count' => 1,
            'additional_ingredients' => [
                'fried-onions' => 1
            ]
        ]
    ],
];

$app = new AppRunner();

$app->prepareOrder($order);