<?php

namespace Blarkinov\PhpDbCourse\Bin;

use Exception;

class Dispatcher
{
    public function getResult(Track $track)
    {

        $className = ucfirst($track->controller) . 'Controller';
        $fullName = "\\Blarkinov\\PhpDbCourse\\Controllers\\$className";

        $controller = new $fullName;

        if (method_exists($controller, $track->action))
            return $controller->{$track->action}($track->params);
        else
            throw new Exception('not found method');
    }
}
