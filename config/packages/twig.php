<?php

declare(strict_types=1);

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig): void {
    $twig
        ->fileNamePattern([
            '*.twig',
        ])
        ->defaultPath('%kernel.project_dir%/src/Infrastructure/templates');
};
