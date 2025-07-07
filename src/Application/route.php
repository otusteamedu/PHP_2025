<?php

use Application\Routing\Route;
use Infrastructure\Http\Controllers\EventController;

Route::get('api/v1/event', ['controller' => EventController::class, 'method' => 'get']);
Route::get('api/v1/event/one', ['controller' => EventController::class, 'method' => 'one']);
Route::post('api/v1/event', ['controller' => EventController::class, 'method' => 'create']);
Route::delete('api/v1/event', ['controller' => EventController::class, 'method' => 'delete']);
