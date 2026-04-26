<?php
declare(strict_types=1);

//pr([file_exists($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php'), ], true, true);

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use App\Presentation\Controller\User\UserController;
use App\Presentation\Controller\TrainingPlan\TrainingPlanController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return static function (App $app) {
    // Add trailing slash middleware
    $app->add(function ($request, $handler) {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path !== '/' && str_ends_with($path, '/')) {
            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath(substr($path, 0, -1));
            
            if($request->getMethod() === 'GET') {
                return (new \Slim\Psr7\Response())
                    ->withHeader('Location', (string)$uri)
                    ->withStatus(301);
            }

            $request = $request->withUri($uri);
        }

        return $handler->handle($request);
    });

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    // User registration and profile
    $app->group('/users', function (Group $group) {
        $group->post('', [UserController::class, 'createUser']);
        $group->get('/{id}', [UserController::class, 'getUser']);
        $group->put('/{id}', [UserController::class, 'updateUser']);
        $group->delete('/{id}', [UserController::class, 'deleteUser']);
    });

    // Training Plans
    $app->group('/training-plan', function (Group $group) {
        $group->post('', [TrainingPlanController::class, 'createTrainingPlan']);
        $group->get('/{id}', [TrainingPlanController::class, 'getTrainingPlan']);
    });

    // Exercises
    $app->get('/exercises', function (Request $request, Response $response) {
        // TODO: Implement exercise list with filtering
        $response->getBody()->write('Exercise list placeholder');
        return $response;
    });

    // RabbitMQ routes
    $app->group('/rabbitmq', function (Group $group) {
        $group->post('/send', function (Request $request, Response $response) {
            // TODO: Implement RabbitMQ message sending
            $response->getBody()->write('RabbitMQ send message placeholder');
            return $response;
        });
    });
};
