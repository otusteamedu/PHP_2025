<?php

$config = [
    'serviceContainer' =>
    [
        'controllers' => [
            [
                'path' => 'Infrastructure/Http',
                'namespace' => 'Blarkinov\Hw1500\Infrastructure\Http',
            ],
        ],

        'services' => [
            [
                'path' => 'Application/UseCase',
                'namespace' => 'Blarkinov\Hw1500\Application\UseCase',
            ],
            [
                'path' => 'Application/Factory',
                'namespace' => 'Blarkinov\Hw1500\Application\Factory',
            ],
            [
                'path' => 'Application/Observer',
                'namespace' => 'Blarkinov\Hw1500\Application\Observer',
            ],
            [
                'path' => 'Domain',
                'namespace' => 'Blarkinov\Hw1500\Domain',
            ],
        ]
    ],
];
