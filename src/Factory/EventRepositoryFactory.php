<?php
declare(strict_types=1);

namespace App\Factory;

use App\Repository\EventRepositoryInterface;
use App\Repository\MongoEventRepository;
use App\Repository\RedisEventRepository;

class EventRepositoryFactory
{
    /**
     * @param array $config
     * @return EventRepositoryInterface
     */
    public static function create(array $config): EventRepositoryInterface
    {
        return match ($config['driver']) {
            'mongodb' => new MongoEventRepository(),
            default   => new RedisEventRepository(),
        };
    }
}
