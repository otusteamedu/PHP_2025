<?php

namespace App\Services;

use Exception;

class RouteService extends Service
{
    public function __construct() {
        include(__DIR__ . '/../route.php');
    }

    /** @var array */
    protected static array $routes = [];

    /** @var array */
    public array $currentRoute = [];

    /**
     * @param string $name
     * @param string $method
     * @return string
     */
    protected static function getRoutePath(string $name, string $method): string {
        $name = explode('?', $name)[0];
        $name = preg_replace('/(^\/)|(\/$)/', '', $name);
        return "$name::$method";
    }

    /**
     * @param string $name
     * @param array{controller: string, method: string} $options
     * @return void
     */
    public static function get(string $name, array $options) {
        self::setRoute($name, "GET", $options);
    }

    /**
     * @param string $name
     * @param array{controller: string, method: string} $options
     * @return void
     */
    public static function post(string $name, array $options) {
        self::setRoute($name, "POST", $options);
    }

    /**
     * @param string $name
     * @param array{controller: string, method: string} $options
     * @return void
     */
    public static function put(string $name, array $options) {
        self::setRoute($name, "PUT", $options);
    }

    /**
     * @param string $name
     * @param array{controller: string, method: string} $options
     * @return void
     */
    public static function delete(string $name, array $options) {
        self::setRoute($name, "DELETE", $options);
    }

    /**
     * @param string $name
     * @param string $method
     * @param array $options
     * @return void
     */
    public static function setRoute(string $name, string $method, array $options) {
        $path = self::getRoutePath($name, $method);

        self::$routes[$path] = [
            'controller' => $options['controller'],
            'method' => $options['method'],
        ];
    }

    /**
     * @param string $uri
     * @param string $method
     * @return array
     * @throws Exception
     */
    public function find(string $uri, string $method): array {
        $path = self::getRoutePath($uri, $method);
        $route = self::$routes[$path];

        if (empty($route)) {
            throw new Exception("Маршрут не найден");
        }

        return $this->currentRoute = $route;
    }
}