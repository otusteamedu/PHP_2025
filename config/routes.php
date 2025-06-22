<?php

declare(strict_types=1);

use App\Infrastructure\Controller\PendingRequestController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    /*$routes->add('lucky_number', '/lucky/number')
        // the controller value has the format [controller_class, method_name]
        ->controller([LuckyController::class, 'number'])
        ->methods(['GET'])

        // if the action is implemented as the __invoke() method of the
        // controller class, you can skip the 'method_name' part:
        // ->controller(BlogController::class)
    ;*/

    $routes->add('form', '/form')
        ->controller([PendingRequestController::class, 'index'])
        ->methods(['GET']);

    $routes->add('formCreateRequest', '/form')
        ->controller([PendingRequestController::class, 'createRequest'])
        ->methods(['POST']);
};
