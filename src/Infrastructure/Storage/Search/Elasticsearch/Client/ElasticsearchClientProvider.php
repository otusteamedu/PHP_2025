<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\Search\Elasticsearch\Client;

use App\Core\Container\Config\Data\DotEnv\DotEnvConfigInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class ElasticsearchClientProvider
{
    private ?Client $esClient = null;

    public function __construct(
        private readonly DotEnvConfigInterface $dotEnvConfig,
    ) {
    }

    public function getClient(): Client
    {
        if ($this->esClient === null) {
            $esUser = $this->getRequiredCredential('ELASTIC_USER');
            $esPassword = $this->getRequiredCredential('ELASTIC_PASSWORD');
            $esHost = $this->getRequiredCredential('ELASTIC_HOST');
            $esPort = $this->getRequiredCredential('ELASTIC_PORT');
            $certsDir = $this->getRequiredCredential('ELASTIC_CERTS_DIR');

            try {
                $this->esClient = ClientBuilder::create()
                    ->setHosts(["https://$esHost:$esPort"])
                    ->setBasicAuthentication($esUser, $esPassword)
                    ->setCABundle("$certsDir/ca/ca.crt")
                    ->build();
            } catch (AuthenticationException $e) {
                throw new \RuntimeException("Authentication error: {$e->getMessage()}");
            }
        }

        return $this->esClient;
    }

    private function getRequiredCredential(string $key): mixed
    {
        if (!$this->dotEnvConfig->has($key)) {
            throw new \RuntimeException("Missing configuration credential: $key");
        }

        return $this->dotEnvConfig->get($key);
    }
}
