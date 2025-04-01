<?php

declare(strict_types=1);

namespace App\Repositories\Redis;

use App\Application;
use App\Repositories\EventStorageRepositoryInterface;
use App\Storage\Redis\ClientBuilder;
use App\Storage\Redis\Config;
use Redis;
use RedisException;

/**
 * Class RedisEventRepository
 * @package App\Repositories\Redis
 */
class RedisEventStorageRepository implements EventStorageRepositoryInterface
{
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var Redis
     */
    private Redis $client;

    /**
     * @throws RedisException
     */
    public function __construct()
    {
        $this->config = new Config(Application::$app->getConfig());
        $this->client = ClientBuilder::create($this->config);
    }

    /**
     * @inheritdoc
     * @throws RedisException
     */
    public function clear(): void
    {
        $this->client->flushAll();
    }
}
