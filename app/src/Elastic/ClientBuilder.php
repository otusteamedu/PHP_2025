<?php declare(strict_types=1);

namespace App\Elastic;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder as ElasticClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

/**
 * Class ClientBuilder
 * @package App\Elastic
 */
class ClientBuilder
{
    /**
     * @param Config $config
     * @return Client
     * @throws AuthenticationException
     */
    public static function create(Config $config): Client
    {
        $host = "https://{$config->getHost()}:{$config->getPort()}";

        return ElasticClientBuilder::create()
            ->setHosts([$host])
            ->setBasicAuthentication($config->getUserName(), $config->getPassword())
            ->setSSLVerification(false)
            ->build();
    }
}
