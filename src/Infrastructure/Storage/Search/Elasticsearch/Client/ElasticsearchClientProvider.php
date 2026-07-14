<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\Search\Elasticsearch\Client;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchClientProvider
{
    private ?Client $esClient = null;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $username,
        private readonly string $password,
        private readonly ?string $caBundlePath = null,
    ) {
    }

    public function getClient(): Client
    {
        if ($this->esClient === null) {
            $scheme = $this->caBundlePath !== null ? 'https' : 'http';
            $hosts = ["{$scheme}://{$this->host}:{$this->port}"];

            $builder = ClientBuilder::create()
                ->setHosts($hosts)
                ->setBasicAuthentication($this->username, $this->password);

            if ($this->caBundlePath !== null) {
                $builder->setCABundle($this->caBundlePath);
            }

            try {
                $this->esClient = $builder->build();
            } catch (\Exception $e) {
                throw new \RuntimeException("Failed to create Elasticsearch client: {$e->getMessage()}");
            }
        }

        return $this->esClient;
    }
}
