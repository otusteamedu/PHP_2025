<?php
namespace App\Factory;

use App\Repository\EventRepositoryInterface;
use App\Repository\RedisEventRepository;
use App\Repository\ElasticEventRepository;
use Predis\Client as RedisClient;
use Elastic\Elasticsearch\ClientBuilder;

class RepositoryFactory {
    public static function create(array $config): EventRepositoryInterface {
        if ($config['storage'] === 'redis') {
            $redis = new RedisClient($config['redis']);
            return new RedisEventRepository($redis);
        } elseif ($config['storage'] === 'elastic') {
            $elastic = ClientBuilder::create()->setHosts([$config['elastic']['host']])->build();
            return new ElasticEventRepository($elastic);
        } else {
            throw new \Exception("Unknown storage type: {$config['storage']}");
        }
    }
}
