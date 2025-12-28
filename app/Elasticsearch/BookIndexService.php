<?php

declare(strict_types=1);

namespace App\Elasticsearch;

use Elastic\Elasticsearch\Client;

final class BookIndexService
{
    private const DEFAULT_INDEX = 'otus-shop';

    public function __construct(
        private readonly Client $client,
        private readonly string $index = self::DEFAULT_INDEX,
    ) {}

    public function ping(): bool
    {
        try {
            $response = $this->client->info();
            return $response->asBool();
        } catch (\Throwable) {
            return false;
        }
    }

    public function bulkImport(string $ndjsonFile): array
    {
        if (!is_file($ndjsonFile)) {
            throw new \InvalidArgumentException(sprintf('File "%s" does not exist.', $ndjsonFile));
        }

        $body = file_get_contents($ndjsonFile);
        if ($body === false) {
            throw new \RuntimeException(sprintf('Cannot read file "%s".', $ndjsonFile));
        }

        $response = $this->client->bulk(['body' => $body]);

        return $response->asArray();
    }

    public function deleteIndex(?string $index = null): array
    {
        $targetIndex = $index ?? $this->index;

        try {
            $response = $this->client->indices()->delete(['index' => $targetIndex]);
        } catch (\Elastic\Elasticsearch\Exception\MissingParameterException $e) {
            throw new \InvalidArgumentException('Index name is required.', 0, $e);
        } catch (\Elastic\Elasticsearch\Exception\ClientResponseException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return $response->asArray();
    }

    public function search(array $query, int $size = 10): array
    {
        $response = $this->client->search([
            'index' => $this->index,
            'size' => $size,
            'body' => [
                'query' => $query,
            ],
        ]);

        return $response->asArray();
    }
}
