<?php

declare (strict_types=1);

namespace App;

use App\Base\ApplicationInterface;
use App\Controllers\IndexController;

class ExampleApplication implements ApplicationInterface
{

    public function __construct()
    {
    }

    public function registerRoutes(): array
    {
        return [
            '/' => [IndexController::class, 'index'],
        ];
    }

    public function registerServices(): array
    {
        return [
            Database::class => function () {
                return new Database(
                    getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASSWORD'),
                );
            },
        ];
    }

    public function boot(): void
    {
    }
}