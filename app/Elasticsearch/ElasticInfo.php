<?php

declare(strict_types=1);

namespace User\Php2025\Elasticsearch;

class ElasticInfo
{
    private const string INDEX_NAME = 'otus-shop';

    public function getIndexName(): string
    {
        return self::INDEX_NAME;
    }
}
