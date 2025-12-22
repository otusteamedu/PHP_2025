<?php

namespace Blarkinov\RabbitMq\App;

use Blarkinov\RabbitMq\Http\Request;

class App
{
    public function run()
    {
        $routes = include __DIR__ . '/../route.php';

        (new Request())->handle($routes);
    }
}
