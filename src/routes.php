<?php
use Slim\App;
use App\Controller\TaskController;

return function (App $app) {
    // Маршруты API
    $app->post('/tasks', [TaskController::class, 'create']);
    $app->get('/tasks/{id}', [TaskController::class, 'status']);
};
