<?php

namespace Blarkinov\PhpDbCourse\App;

use Blarkinov\PhpDbCourse\Http\Request;

class App
{
    public function run()
    {
        (new Request())->handle();
    }
}
