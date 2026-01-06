<?php

declare(strict_types=1);

namespace Otus\Cache\Factory;

use Otus\Cache\Entity\Conditions;

final class ConditionsFactory
{
    /**
     * @param array $data
     *
     * @return Conditions
     */
    public static function factory(array $data): Conditions
    {
        [
            'conditions' => $conditions,
        ] = $data;

        return new Conditions($conditions);
    }
}
