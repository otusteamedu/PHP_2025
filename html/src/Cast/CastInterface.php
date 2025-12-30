<?php

declare(strict_types=1);

namespace Otus\DataMapper\Cast;

interface CastInterface
{
    /**
     * @return mixed
     */
    public function getCast(): mixed;
}
