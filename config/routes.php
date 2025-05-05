<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('news_index', '/api/index')
        ->controller([\App\Infrastructure\Http\News\Controller\NewsIndexController::class, 'index'])
        ->methods(['GET']);
    $routes->add('generate_report', '/api/generate_report')
        ->controller([\App\Infrastructure\Http\News\Controller\GenerateNewsController::class, 'generateReport'])
        ->methods(['POST']);
    $routes->add('news_create', '/api/create_news')
        ->controller([\App\Infrastructure\Http\News\Controller\NewsCreateController::class, 'create'])
        ->methods(['POST']);
};
