<?php

declare(strict_types=1);

namespace Otus\Cache\Adapter;

use Memcached;
use Otus\Cache\Entity\Push;
use Otus\Cache\Entity\Search;
use Otus\Cache\Factory\PushFactory;

final class PhpMemcachedAdapter extends AbstractAdapter
{
    /**
     * @param Memcached|null $driver
     */
    public function __construct(private ?Memcached $driver = null)
    {
        if (!$this->driver instanceof Memcached) {
            $this->driver = new Memcached();

            $this->driver->addServers([
                ['localhost', 11211],
            ]);
        }
    }

    /**
     * @param Push $push
     */
    public function push(Push $push): void
    {
        $key = $this->getKey($push->conditions->toArray());

        $list = $this->driver->get($key) ?: '[]';

        $merge = array_merge(json_decode($list, true), $push->toArray());

        $this->driver->set($key, json_encode($merge));
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

        $list = array_values(
            array_map(static function (string $json): Push {
                return PushFactory::factory(json_decode($json, true));
            }, $this->driver->getMulti($keys) ?: [])
        );

        uasort($list, static function (Push $a, Push $b): int {
            return ($a->priority > $b->priority) ? -1 : 1;
        });

        return $list;
    }
}
