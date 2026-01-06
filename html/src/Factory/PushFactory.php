<?php

declare(strict_types=1);

namespace Otus\Cache\Factory;

use Otus\Cache\Entity\Push;

final class PushFactory
{
    /**
     * @param array $data
     *
     * @return Push
     */
    public static function factory(array $data): Push
    {
        [
            'priority' => $priority,
        ] = $data;

        $conditions = ConditionsFactory::factory($data);

        return new Push($priority, $conditions);
    }
}
