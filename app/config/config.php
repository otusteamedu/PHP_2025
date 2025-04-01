<?php

$config = [
    'storage' => 'redis',
    'components' => [],

    'redisHost' => getenv('REDIS_CONTAINER'),
    'redisPort' => getenv('REDIS_PORT'),

    'elasticHost' => getenv('ELASTIC_CONTAINER'),
    'elasticPort' => getenv('ELASTIC_PORT'),
    'elasticUsername' => getenv('ELASTIC_USERNAME'),
    'elasticPassword' => getenv('ELASTIC_PASSWORD'),
    'elasticStorageName' => getenv('ELASTIC_STORAGE_NAME'),
    'elasticStorageSettings' => [
        'mappings' => [
            'properties' => [
                'priority' => [
                    'type' => 'integer',
                ],
                'event' => [
                    'properties' => [
                        'id' => [
                            'type' => 'integer',
                        ],
                        'name' => [
                            'type' => 'text',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

$storage = $config['storage'] ?? null;

if ($storage == 'redis') {
    $config['components'] = [
        'storageRepository' => App\Repositories\Redis\RedisEventStorageRepository::class,
        'eventRepository' => App\Repositories\Redis\RedisEventRepository::class,
    ];
} elseif ($storage == 'elasticsearch') {
    $config['components'] = [
        'storageRepository' => App\Repositories\Elastic\ElasticEventStorageRepository::class,
        'eventRepository' => App\Repositories\Elastic\ElasticEventRepository::class,
    ];
}

return $config;
