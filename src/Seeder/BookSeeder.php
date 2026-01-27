<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Seeder;

use Dinargab\Homework12\Configuration;
use Elastic\Elasticsearch\Client;
use InvalidArgumentException;
use SplFileObject;

class BookSeeder
{

    private Configuration $config;
    private Client $client;
    public function __construct(Client $client, Configuration $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function seedDb()
    {
        $fileArray = $this->loadBooks($this->config->getFilePath());
        $this->bulkUpload($fileArray);
    }

    private function loadBooks(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("Invalid filepath: file does not exists");
        }
        $requestBody = [];
        $file = new SplFileObject($filePath);
        while (!$file->eof()) {

            // Echo one line from the file.
            $line = $file->fgets();
            if (!empty($line)) {
                $requestBody[] = json_decode($line, true);
            }
        }

        return $requestBody;
    }


    private function bulkUpload($array): void
    {
        $chunkedArray = array_chunk($array, 1000);
        foreach ($chunkedArray as $chunk) {
            $this->client->bulk([
                'index' => $this->config->getIndexName(),
                'body' => $chunk
            ]);
        }
    }
}
