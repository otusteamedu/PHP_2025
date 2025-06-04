<?php

declare(strict_types=1);

use Infrastructure\Http\Controllers\OrderController;

return [
    '/orders/list' => ['GET', [OrderController::class, 'index']],
    '/orders/new_order' => ['POST', [OrderController::class, 'store']],
    '/orders/pizza' => ['GET', [OrderController::class, 'makePizza']],
    '/orders/{id}' => ['GET', [OrderController::class, 'show']],
    '/orders/{id}/pay' => ['POST', [OrderController::class, 'orderPay']],
];

