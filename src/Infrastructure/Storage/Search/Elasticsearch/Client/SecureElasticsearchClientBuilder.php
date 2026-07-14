<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\Search\Elasticsearch\Client;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use Elastic\Elasticsearch\Client;

class SecureElasticsearchClientBuilder
{
    public function __construct(
        private readonly DotEnvConfigInterface $dotEnvConfig,
    ) {
    }

    public function build(): Client
    {
        $host = $this->getRequiredCredential('ELASTIC_HOST');
        $port = (int) $this->getRequiredCredential('ELASTIC_PORT');
        $user = $this->getRequiredCredential('ELASTIC_USER');
        $password = $this->getRequiredCredential('ELASTIC_PASSWORD');

        $caBundlePath = $this->dotEnvConfig->get('ELASTIC_CA_BUNDLE');
        if ($caBundlePath === null || !file_exists((string) $caBundlePath)) {
            $caBundlePath = null;
        }

        $provider = new ElasticsearchClientProvider(
            host: $host,
            port: $port,
            username: $user,
            password: $password,
            caBundlePath: $caBundlePath,
        );

        return $provider->getClient();
    }

    private function getRequiredCredential(string $key): mixed
    {
        if (!$this->dotEnvConfig->has($key)) {
            throw new \RuntimeException("Missing configuration credential: $key");
        }

        return $this->dotEnvConfig->get($key);
    }
}
