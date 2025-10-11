<?php

namespace Blarkinov\RedisCourse\App;

use Blarkinov\RedisCourse\Http\Request;

class App
{
    public function run()
    {
        $routes = include __DIR__ . '/../route.php';

        (new Request())->handle($routes);
    }
}
