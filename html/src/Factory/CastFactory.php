<?php

declare(strict_types=1);

namespace Otus\DataMapper\Factory;

use Otus\DataMapper\Cast\CastInterface;
use Otus\DataMapper\Cast\DateTime;
use Otus\DataMapper\Cast\Dummy;

final class CastFactory
{
    /**
     * @param mixed $value
     *
     * @return CastInterface
     */
    public static function factory(mixed $value): CastInterface
    {
        return match (true) {
            $value instanceof \DateTime => new DateTime($value),

            default => new Dummy($value),
        };
    }
}
