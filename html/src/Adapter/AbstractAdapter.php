<?php

declare(strict_types=1);

namespace Otus\Cache\Adapter;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @param array $list
     *
     * @return string
     */
    protected function getKey(array $list): string
    {
        return hash('md5', json_encode($list));
    }
}
