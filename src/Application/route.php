<?php

use Application\Routing\Route;
use Infrastructure\Http\Controllers\EventController;

Route::get('event', ['controller' => EventController::class, 'method' => 'get']);
Route::get('event/one', ['controller' => EventController::class, 'method' => 'one']);
Route::post('event', ['controller' => EventController::class, 'method' => 'create']);
Route::delete('event', ['controller' => EventController::class, 'method' => 'delete']);
