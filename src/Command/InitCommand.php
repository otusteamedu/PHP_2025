<?php

namespace App\Command;

use App\Data\DataLoader;
use App\Index\IndexConfigFactory;
use App\Index\IndexManager;
use App\Service\ElasticsearchClient;

class InitCommand
{
    public function execute(array $options = []): int
    {
        try {
            $indexName = $options['index-name'] ?? 'otus-shop';
            
            // Create Elasticsearch client
            $elasticsearchClient = new ElasticsearchClient();
            
            // Create index manager
            $indexManager = new IndexManager($elasticsearchClient);
            
            // Check if index already exists
            if ($indexManager->indexExists($indexName)) {
                echo "Индекс '$indexName' уже существует\n";
                return 0;
            }
            
            // Create index configuration using factory
            $indexConfig = IndexConfigFactory::create('default', ['name' => $indexName]);
            
            // Create index
            $indexManager->createIndex($indexConfig);
            echo "Индекс '$indexName' успешно создан\n";
            
            // Load data
            $dataLoader = new DataLoader();
            $dataFilePath = __DIR__ . '/../Data/books.json';
            
            $dataLoader->load($dataFilePath, $indexName, $elasticsearchClient->getClient());
            echo "Данные загружены!\n";
            
            return 0;
        } catch (\Exception $e) {
            echo "Ошибка: " . $e->getMessage() . "\n";
            return 1;
        }
    }
}