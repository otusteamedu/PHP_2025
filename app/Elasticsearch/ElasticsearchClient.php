<?php

declare(strict_types=1);

namespace App\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

final class ElasticsearchClient
{
    public function __construct(
        private readonly string $host,
        private readonly ?string $username = null,
        private readonly ?string $password = null,
        private readonly bool $verifySSL = true,
    ) {}

    public function create(): Client
    {
        $builder = ClientBuilder::create()
            ->setHosts([$this->host])
            ->setSSLVerification($this->verifySSL);

        if ($this->username !== null && $this->password !== null) {
            $builder->setBasicAuthentication($this->username, $this->password);
        }

        return $builder->build();
    }
}
