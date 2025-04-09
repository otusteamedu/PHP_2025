<?php

use App\Controllers\EventController;
use App\Services\RouteService;

RouteService::post('event/get', ['controller' => EventController::class, 'method' => 'get']);
RouteService::post('event/create', ['controller' => EventController::class, 'method' => 'create']);
RouteService::delete('event/delete', ['controller' => EventController::class, 'method' => 'delete']);
