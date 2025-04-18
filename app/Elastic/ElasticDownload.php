<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Elastic;

use Exception;
use JsonException;
use RuntimeException;

class ElasticDownload
{

    private ElasticClient $elastic;

    public function __construct(ElasticClient $elastic)
    {
        $this->elastic = $elastic;
    }

    /**
     * @throws JsonException
     */
    public function loadData(string $filePath, string $indexName): void
    {
        if (file_exists($filePath)) {
            $body = [];
            $arFileLines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($arFileLines as $line) {
                if ($json = json_decode($line, true, 512, JSON_THROW_ON_ERROR)) {
                    $body[] = [
                        'index' => [
                            '_index' => $indexName
                        ]
                    ];
                    $body[] = $json;
                } else {
                    echo 'Ошибка в JSON:' . $line . PHP_EOL;
                }
            }

            $client = $this->elastic->getClient();

            if (!empty($body)) {
                try {
                    $response = $client->bulk(['body' => $body]);

                    if (!empty($response['errors'])) {
                        echo 'Данные не загружены!';
                    } else {
                        echo 'Данные загружены.';
                    }
                } catch (Exception $e) {
                    $requestInfo = $client->transport->getLastConnection()->getLastRequestInfo();

                    if (!empty($requestInfo['response']) && ($response = $requestInfo['response'])) {
                        if (isset($response['error']) && ($error = $response['error']) && $error instanceof RuntimeException) {
                            echo $error->getMessage();
                        }

                        if (!empty($response['reason'])) {
                            echo $response['reason'];
                        }
                    } else {
                        echo 'Ошибка Elasticsearch: ' . $e->getMessage();
                    }
                }
            }
        } else {
            echo 'Файл ' . $filePath . 'не найден!';
        }
    }
}
