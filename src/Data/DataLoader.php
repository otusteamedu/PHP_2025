<?php

namespace App\Data;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use JsonException;

class DataLoader
{
    /**
     * @param string $filePath
     * @param string $indexName
     * @param Client $client
     * @return void
     * @throws JsonException
     * @throws \Exception
     */
    public function load(string $filePath, string $indexName, Client $client): void
    {
        if (!file_exists($filePath)) {
            throw new \Exception('Файл ' . $filePath . ' не найден!');
        }

        $body = [];
        $fileLines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($fileLines as $line) {
            if ($json = json_decode($line, true, 512, JSON_THROW_ON_ERROR)) {
                $body[] = [
                    'index' => [
                        '_index' => $indexName
                    ]
                ];
                $body[] = $json;
            } else {
                throw new JsonException('Ошибка в JSON:' . $line . PHP_EOL);
            }
        }

        if (!empty($body)) {
            try {
                $client->bulk(['body' => $body]);
            } catch (ClientResponseException $e) {
                $requestInfo = $client->getTransport()->getLastRequest();

                if (!empty($requestInfo['response']) && ($response = $requestInfo['response'])) {
                    if (isset($response['error']) && ($error = $response['error'])) {
                        throw new \Exception($error['reason'] ?? $error->getMessage());
                    }

                    if (!empty($response['reason'])) {
                        throw new \Exception($response['reason']);
                    }
                }
                
                throw new \Exception('Ошибка Elasticsearch: ' . $e->getMessage());
            } catch (\Exception $e) {
                throw new \Exception('Ошибка при загрузке данных: ' . $e->getMessage());
            }
        }
    }
}