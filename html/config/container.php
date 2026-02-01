<?php

declare(strict_types=1);

use Otus\Food\Application\Strategy\Burger;
use Otus\Food\Application\Strategy\Harvester;
use Otus\Food\Application\Strategy\Pizza\Chef;
use Otus\Food\Application\Strategy\Sandwich;
use Otus\Food\Infrastructure\Bus\Bus;
use Otus\Food\Presentation\Console\Kitchen;

return [
    'singletons' => [
        // Application
        Harvester::class => static function (): Harvester {
            return new Harvester([
                'burger' => new Burger(),
                'sandwich' => new Sandwich(),
                'pizza:chef' => new Chef(),
            ]);
        },
        // Infrastructure
        Bus::class => static function (): Bus {
            return new Bus([
                'kitchen' => Kitchen::class,
            ]);
        },
        // Presentation
    ],
    'definitions' => [
        // Application
        // Infrastructure
        // Presentation
    ],
];
