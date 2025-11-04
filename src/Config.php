<?php

namespace App;

class Config
{
    private string $elasticsearchHost;
    private string $booksFile;
    private string $indexName;

    public function __construct()
    {
        $this->elasticsearchHost = 'http://localhost:9200';
        $this->booksFile = 'dist/books.json';
        $this->indexName = 'otus-shop';
    }

    public function getElasticsearchHost(): string
    {
        return $this->elasticsearchHost;
    }

    public function getBooksFile(): string
    {
        return $this->booksFile;
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }
}