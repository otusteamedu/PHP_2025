<?php

declare(strict_types=1);

namespace Dinargab\Homework12;

class Configuration
{
    private string $elasticHost;
    private string $indexName;
    private string $filePath;

    public function __construct()
    {
        $this->elasticHost = getenv("ELASTIC_HOST");
        $this->indexName = getenv("INDEX_NAME") ? getenv("INDEX_NAME") : "otus-shop";
        $this->filePath = getenv("FILE_PATH") ? getenv("FILE_PATH") : "./books.json";
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getElasticHost(): string
    {
        return $this->elasticHost;
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }
}
