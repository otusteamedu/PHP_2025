<?php

declare(strict_types=1);

use App\Infrastructure\Controller\RequestProcessingController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('apiRequestId', '/api/request/{id}')
        ->controller([RequestProcessingController::class, 'index'])
        ->methods(['GET']);

    $routes->add('apiRequest', '/api/createRequest/')
        ->controller([RequestProcessingController::class, 'createRequest'])
        ->methods(['POST']);
};
