<?php

return [
    'properties' => [
        'category' => [
            'type' => 'keyword'
        ],
        'price' => [
            'type' => 'integer'
        ],
        'sku' => [
            'type' => 'keyword'
        ],
        'title' => [
            'type' => 'text',
            'analyzer' => 'my_russian',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword',
                    'ignore_above' => 256
                ]
            ]
        ],
        'stock' => [
            'type' => 'nested',
            'properties' => [
                'shop' => [
                    'type' => 'keyword',
                ],
                'stock' => [
                    'type' => 'integer'
                ]
            ]
        ]
    ]
];