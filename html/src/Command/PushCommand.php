<?php

declare(strict_types=1);

namespace Otus\Cache\Command;

use Otus\Cache\Adapter\AdapterInterface;
use Otus\Cache\Factory\PushFactory;
use Otus\Cache\Factory\StoreFactory;

readonly class PushCommand
{
    /**
     * @var AdapterInterface
     */
    protected AdapterInterface $store;

    public function __construct()
    {
        $this->store = StoreFactory::factory();
    }

    /**
     * @param string $json
     *
     * @return int
     */
    public function __invoke(string $json): int
    {
        $data = json_decode($json, true);

        $push = PushFactory::factory($data);

        $this->store->push($push);

        return 0;
    }
}
