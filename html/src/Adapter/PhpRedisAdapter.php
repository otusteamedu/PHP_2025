<?php

declare(strict_types=1);

namespace Otus\Cache\Adapter;

use Otus\Cache\Entity\Push;
use Otus\Cache\Entity\Search;
use Otus\Cache\Factory\PushFactory;
use Redis;

final class PhpRedisAdapter extends AbstractAdapter
{
    /**
     * @param Redis|null $driver
     */
    public function __construct(private ?Redis $driver = null)
    {
        if (!$this->driver instanceof Redis) {
            $this->driver = new Redis();

            $this->driver->connect('localhost');
        }
    }

    /**
     * @param Push $push
     */
    public function push(Push $push): void
    {
        $key = $this->getKey($push->conditions->toArray());

        $this->driver->zAdd($key, $push->priority, json_encode($push->toArray()));
    }

    /**
     * @param Search $search
     *
     * @return array
     */
    public function search(Search $search): array
    {
        $keys = [
            $this->getKey($search->conditions->toArray()),
        ];

        foreach ($search->conditions->toArray() as $field => $value) {
            $keys[] = $this->getKey([$field => $value]);
        }

        $result = $this->driver->zunion($keys);

        return array_values(
            array_map(static function (string $json): Push {
                return PushFactory::factory(json_decode($json, true));
            }, array_reverse($result))
        );
    }
}
