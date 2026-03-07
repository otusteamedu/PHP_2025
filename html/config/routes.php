<?php

declare(strict_types=1);

use Otus\Queue\Infrastructure\Http\Method;
use Otus\Queue\Presentation\Http\Chat\HistoryController;
use Otus\Queue\Presentation\Http\Chat\SseController;
use Otus\Queue\Presentation\Http\Chat\StoreController;
use Otus\Queue\Presentation\Http\FallbackController;

return [
    ['method' => Method::GET, 'pattern' => '/sse', 'controller' => SseController::class],
    ['method' => Method::GET, 'pattern' => '/', 'controller' => HistoryController::class],
    ['method' => Method::POST, 'pattern' => '/', 'controller' => StoreController::class],
    /**
     * Fallback.
     */
    'fallback' => [
        'controller' => FallbackController::class,
    ],
];
