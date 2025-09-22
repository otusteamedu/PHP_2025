<?php
declare(strict_types=1);

use App\Application\ApplicationFactory;
use App\Database\Database;
use App\Database\Postgres;
use Symfony\Component\Console\Application;
use function DI\autowire;
use function DI\factory;

return [
    Application::class => factory(ApplicationFactory::class),
    Database::class => autowire(Postgres::class),
];
