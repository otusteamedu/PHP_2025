<?php

declare(strict_types=1);

use App\Domain\BracketValidator;
use App\Application\BracketService;
use App\UserInterface\HttpController;

return [

    'bracketValidator' => function () {
        return new BracketValidator();
    },

    'bracketService' => function ($container) {
        return new BracketService(
            $container->get('bracketValidator')
        );
    },

    'httpController' => function ($container) {
        return new HttpController(
            $container->get('bracketService')
        );
    },
];
