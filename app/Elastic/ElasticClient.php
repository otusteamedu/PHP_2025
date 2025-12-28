<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Elastic;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ElasticClient
{
    private const LOGIN = 'elastic';
    private const PASSWORD = 'changeme';
    private const HOST = 'elasticsearch';
    private const PORT = '9200';

    private Client $client;

    public function __construct(string $host = self::HOST)
    {
        $this->client = ClientBuilder::create()->setHosts([$host . ':' . self::PORT])->setBasicAuthentication(self::LOGIN, self::PASSWORD)->build();
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
