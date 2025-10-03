<?php
declare(strict_types=1);

use App\Application\ApplicationFactory;
use Symfony\Component\Console\Application;
use function DI\factory;

return [
    Application::class => factory(ApplicationFactory::class),
];
