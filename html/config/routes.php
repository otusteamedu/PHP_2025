<?php

declare(strict_types=1);

use Otus\Queue\Infrastructure\Http\Method;
use Otus\Queue\Presentation\Http\Chat\HistoryController;
use Otus\Queue\Presentation\Http\Chat\IndexController;
use Otus\Queue\Presentation\Http\Chat\SseController;
use Otus\Queue\Presentation\Http\Chat\StoreController;
use Otus\Queue\Presentation\Http\FallbackController;
use Otus\Queue\Presentation\Http\Swagger\ApiController;
use Otus\Queue\Presentation\Http\Swagger\UiController;

return [
    ['method' => Method::GET, 'pattern' => '/', 'controller' => IndexController::class],
    ['method' => Method::GET, 'pattern' => '/sse', 'controller' => SseController::class],
    ['method' => Method::GET, 'pattern' => '/api/history', 'controller' => HistoryController::class],
    ['method' => Method::POST, 'pattern' => '/api/store', 'controller' => StoreController::class],
    /**
     * Swagger.
     */
    ['method' => Method::GET, 'pattern' => '/swagger/api', 'controller' => ApiController::class],
    ['method' => Method::GET, 'pattern' => '/swagger/ui', 'controller' => UiController::class],
    /**
     * Fallback.
     */
    'fallback' => [
        'controller' => FallbackController::class,
    ],
];
