<?php

namespace Pryaniki\App\Presentation\Controllers\Commands\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Pryaniki\App\Application\Services\ProductImporterInterface;

class ImportAction implements ProductImporterInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    public function import(string $filePath): void
    {
        $this->importFromFile($filePath);
    }

    private function importFromFile(string $filePath): void
    {
        $body = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $body[] = json_decode($line, true);
        }
        $response = $this->client->bulk(['body' => $body])->asArray();

        if ($response['errors']) {
            throw new \RuntimeException('Import failed');
        }
    }
}