<?php
declare(strict_types=1);

namespace App\Infrastructure\Service;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder as ElasticClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class ClientBuilder
{
    /**
     * @throws AuthenticationException
     */
    public static function build(string $host, string $port, string $user, string $password): Client
    {
        return ElasticClientBuilder::create()
            ->setHosts(["{$host}:{$port}"])
            ->setBasicAuthentication($user, $password)
            ->build();
    }

}