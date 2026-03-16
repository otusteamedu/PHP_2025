<?php

namespace Crowley\App\Config;

class Routes
{
    private array $routes;

    public function __construct() {
        $this->routes = [
            "/" => ["HomeController", "index"],
            "/add_event" => ["HomeController", "addEvent"],
            "/get_events" => ["HomeController", "getEvents"],
            "/get_event" => ["HomeController", "getEvent"],
            "/clear_events" => ["HomeController", "clearEvents"],

        ];
    }

    public function getRoutes(): array {
        return $this->routes;
    }

}