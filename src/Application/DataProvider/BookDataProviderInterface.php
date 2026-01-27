<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Application\DataProvider;

use Dinargab\Homework14\Application\DTO\BookDTO;

interface BookDataProviderInterface
{
    public function load(): \Generator;
}