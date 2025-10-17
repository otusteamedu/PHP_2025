<?php

declare(strict_types=1);

namespace App\Infrastructure\Elasticsearch;

use App\Infrastructure\Config;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

final readonly class ClientFactory
{
    public function __construct(private Config $config)
    {
    }

    /**
     * @throws AuthenticationException
     */
    public function create(): Client
    {
        $url = rtrim((string)$this->config->get('ELASTIC_URL', 'http://localhost:9200'), '/');
        $username = $this->config->get('ELASTIC_USERNAME');
        $password = $this->config->get('ELASTIC_PASSWORD');

        $builder = ClientBuilder::create()
            ->setHosts([$url])
            ->setSSLVerification(false);

        if ($username !== null && $password !== null) {
            $builder->setBasicAuthentication($username, $password);
        }

        return $builder->build();
    }
}
