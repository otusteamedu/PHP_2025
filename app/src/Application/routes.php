<?php
declare(strict_types=1);

use App\Application\Router\Route;
use App\EventSystem\ApiActions\AddEvent;
use App\EventSystem\ApiActions\ClearEvents;
use App\EventSystem\ApiActions\FindEvent;

return [
    Route::get('/events', FindEvent::class),
    Route::post('/events', AddEvent::class),
    Route::delete('/events', ClearEvents::class),
];
