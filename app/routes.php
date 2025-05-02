<?php

declare(strict_types=1);

use App\Application\Actions\AddUrl\AddUrlAction;
use App\Application\Actions\News\ListNewsAction;
use App\Application\Actions\News\ReportNewsAction;
use App\Application\Actions\News\ViewNewsAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id:[0-9]+}', ViewUserAction::class);
    });
    $app->group('/news', function (Group $group) {
        $group->get('', ListNewsAction::class);
        $group->get('/report/{ids:.*}', ReportNewsAction::class);
        $group->post('/report', ReportNewsAction::class);
        $group->get('/{id:[0-9]+}', ViewNewsAction::class);
        $group->post('/add', AddUrlAction::class);
    });
};
