<?php

declare(strict_types=1);

namespace Otus\Cache\Adapter;

use Otus\Cache\Entity\Push;
use Otus\Cache\Entity\Search;

interface AdapterInterface
{
    /**
     * @param Push $push
     */
    public function push(Push $push): void;

    /**
     * @param Search $search
     *
     * @return array
     */
    public function search(Search $search): array;
}
