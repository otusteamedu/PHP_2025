<?php

declare(strict_types=1);

namespace Dinargab\Homework11;

use Dinargab\Homework11\Controllers\MainController;
use Dinargab\Homework11\Repositories\EventMongoRepository;
use Dinargab\Homework11\Repositories\EventRedisRepository;

class App
{
    public function __construct()
    {
        $router = new Router();
        switch (strtolower(getenv("STORAGE") ? getenv("STORAGE") : 'redis')) {
            case 'mongo':
                $username = getenv("MONGODB_USERNAME");
                $password = getenv("MONGODB_PASSWORD");
                $host = getenv("MONGODB_HOST");
                $port = getenv("MONGODB_PORT");
                $connectionString = "mongodb://$username:$password@$host:$port/";
                $repository = new EventMongoRepository(new \MongoDB\Client($connectionString));
                break;
            default:
                $repository = new EventRedisRepository(new \Redis([
                    'host' => getenv("REDIS_HOST") ? getenv("REDIS_HOST") : 'redis',
                    'port' => getenv("REDIS_PORT") ? getenv("REDIS_PORT") : 6379
                ]));
                break;
        }


        $router->post("/find", function() use ($repository) {
            (new MainController($repository))->findEvent();
        });
        $router->post("/add", function() use ($repository) {
            (new MainController($repository))->addEvent();
        });
        $router->get("/delete-all", function() use($repository) {
            (new MainController($repository))->deleteAll();
        });
        $router->run();
    }
}