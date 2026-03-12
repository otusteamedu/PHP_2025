<?php

declare(strict_types=1);

use App\UserInterface\Api\Controller;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->post('/api/request', [Controller::class, 'request']);
    $app->get('/api/information/{id}', [Controller::class, 'information']);
};
