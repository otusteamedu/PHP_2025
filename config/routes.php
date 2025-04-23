<?php

use App\Infrastructure\Http\News\Controller\NewsController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Requirement\Requirement;

return function (RoutingConfigurator $routes): void {
    $routes->add('news_index', '/api/index')->controller([NewsController::class, 'index'])
        ->methods(['GET']);
    $routes->add('generate_report', '/api/generate_report')->controller([NewsController::class, 'generateReport'])
        ->methods(['POST']);
    $routes->add('news_create', '/api/create_news')
        ->controller([NewsController::class, 'create'])
        ->methods(['POST']);
};
