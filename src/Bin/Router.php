<?php

namespace Blarkinov\PhpDbCourse\Bin;

use Exception;

class Router
{
    public function getTrack(array $routes, string $uri, string $method)
    {

        foreach ($routes as $route) {

            $pattern = $this->createPattern($route->path);

            if (preg_match($pattern, $uri, $params) && $method===$route->method) {
                $params = $this->clearParams($params);

                return new Track($route->controller, $route->action, $method, $params);
            }
        };

        return new Track('main', 'notFound','GET');
    }

    private function createPattern($path)
    {
        return '#^' . preg_replace('#/:([^/]+)#', '/(?<$1>[^/]+)', $path) . '/?$#';
    }

    private function clearParams($params)
    {
        $result = [];

        foreach ($params as $key => $param) {
            if (!is_int($key)) {
                $result[$key] = $param;
            }
        }

        return $result;
    }
}
