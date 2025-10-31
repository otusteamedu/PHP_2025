<?php

declare(strict_types=1);

/**
 * Конфигурация подключения к Elasticsearch
 */
return [
    'host' => $_ENV['ELASTICSEARCH_HOST'] ?? 'elasticsearch',
    'port' => $_ENV['ELASTICSEARCH_PORT'] ?? '9200',
    'index' => $_ENV['ELASTICSEARCH_INDEX'] ?? 'otus-shop',
];

