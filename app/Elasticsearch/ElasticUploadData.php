<?php

declare(strict_types=1);

namespace User\Php2025\Elasticsearch;

class ElasticUploadData
{
    private ElasticClient $elasticClient;

    public function __construct(ElasticClient $elasticClient)
    {
        $this->elasticClient = $elasticClient;
    }

    public function uploadFile()
    {
        $elasticIndex = new ElasticIndex($this->elasticClient);
        if ($elasticIndex->indexExists() === false) {
            echo 'Идекс не существует. Создайте пожалуйста индекс.';
            exit();
        }

        $uploadFile = file(__DIR__ . '/../../upload/books.json');
        $indexName = new ElasticInfo();
        $body = '';

        foreach ($uploadFile as $line) {
            $data = json_decode($line, true);
            if ($data === null) {
                continue;
            }

            $body .= json_encode(['index' => ['_index' => $indexName->getIndexName()]]) . "\n";
            $body .= json_encode($data) . "\n";
        }

        $client = $this->elasticClient->getClient();

        try {
            $client->bulk(['body' => $body]);
            echo 'Данные загружены.';
        } catch (\Exception $exception) {
            echo 'Ошибка при загрузке данных: ' . $exception->getMessage();
        }
    }
}
