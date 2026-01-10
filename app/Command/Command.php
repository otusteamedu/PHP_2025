<?php

declare (strict_types=1);

namespace User\Php2025\Command;

use User\Php2025\Elasticsearch\ElasticClient;
use User\Php2025\Elasticsearch\ElasticIndex;
use User\Php2025\Elasticsearch\ElasticSearch;
use User\Php2025\Elasticsearch\ElasticUploadData;

class Command
{
    public function run(string $action, ?string $title, ?string $price): void
    {
        $client = new ElasticClient;

        if ($action == 'create') {
            $elasticIndex = new ElasticIndex($client);
            $elasticIndex->createIndex();
        }

        if ($action == 'delete') {
            $elasticIndex = new ElasticIndex($client);
            $elasticIndex->deleteIndex();
        }

        if ($action == 'upload') {
            $elasticUpload = new ElasticUploadData($client);
            $elasticUpload->uploadFile();
        }
        if ($action == 'search') {
            $elasticSearch = new ElasticSearch($client);
            $elasticSearch->search($title, $price);
        }
    }
}
