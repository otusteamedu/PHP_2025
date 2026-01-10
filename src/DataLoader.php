<?php

namespace App;

class DataLoader {
    private ElasticClient $elastic;

    public function __construct(ElasticClient $elastic) {
        $this->elastic = $elastic;
    }

    public function loadData(string $filePath, string $indexName): void {
        if (!file_exists($filePath)) {
            echo "Файл не найден: $filePath\n";
            return;
        }

        $bulkData = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $json = json_decode($line, true);
            if ($json === null) {
                echo "Ошибка в JSON: $line\n";
                continue;
            }

            $bulkData[] = [
                'index' => [
                    '_index' => $indexName
                ]
            ];
            $bulkData[] = $json;
        }

        if (!empty($bulkData)) {
            $client = $this->elastic->getClient();
            $params = ['body' => $bulkData];

            try {
                $response = $client->bulk($params);
                if (!empty($response['errors'])) {
                    echo "Ошибка при загрузке данных.\n";
                } else {
                    echo "Данные загружены.\n";
                }
            } catch (\Exception $e) {
                echo "Ошибка Elasticsearch: " . $e->getMessage() . "\n";
            }
        }
    }
}
