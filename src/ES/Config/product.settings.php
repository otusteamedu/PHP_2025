<?php

return [
    'analysis' => [
        'char_filter' => [
            'eCharFilter' => [
                'type' => 'mapping',
                'mappings' => [
                    'Ё => Е',
                    'ё => е',
                    ', => .'
                ]
            ]
        ],
        'filter' => [
            'ru_stop' => [
                'type' => 'stop',
                'stopwords' => '_russian_'
            ],
            'ru_stemmer' => [
                'type' => 'stemmer',
                'language' => 'russian'
            ]
        ],
        'analyzer' => [
            'my_russian' => [
                'tokenizer' => 'standard',
                'filter' => [
                    'lowercase',
                    'ru_stop',
                    'ru_stemmer',
                ],
                'char_filter' => ['eCharFilter']
            ],
        ]
    ]
];