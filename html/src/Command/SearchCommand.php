<?php

declare(strict_types=1);

namespace Otus\Cache\Command;

use Otus\Cache\Adapter\AdapterInterface;
use Otus\Cache\Factory\SearchFactory;
use Otus\Cache\Factory\StoreFactory;

readonly class SearchCommand
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

        $search = SearchFactory::factory($data);

        $result = $this->store->search($search);

        $this->render($result);

        return 0;
    }

    /**
     * @param array $list
     */
    protected function render(array $list): void
    {
        print_r($list);
    }
}
