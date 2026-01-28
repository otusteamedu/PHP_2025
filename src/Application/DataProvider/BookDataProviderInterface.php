<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Application\DataProvider;

use Generator;

interface BookDataProviderInterface
{
    public function load(): Generator;
}