<?php

declare(strict_types=1);

namespace App;

use App\API\EventAPI;

class App
{
    public function run(): void
    {
        $api = new EventAPI();
        $api->handleRequest();
    }
}
