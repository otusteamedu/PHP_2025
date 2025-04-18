<?php

use App\Infrastructure\Http\News\Controller\NewsController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Requirement\Requirement;

return function (RoutingConfigurator $routes): void {
    $routes->add('news_index', '/api/index')->controller([NewsController::class, 'index'])
        ->methods(['GET']);
    $routes->add('generate_report', '/api/generate_report')->controller([NewsController::class, 'generateReport'])
        ->methods(['POST']);

    //TODO use only POST
    $routes->add('news_create', '/api/create_news')
        ->controller([NewsController::class, 'create'])
        ->methods(['POST']);

//    $routes->add('teacher_get_by_id', '/teachers/{id}')->controller([TeacherController::class, 'getById'])
//        ->requirements(['id' => Requirement::DIGITS])
//        ->methods(['GET']);
//    $routes->add('teacher_update', 'teacher-update/{id}')->controller([TeacherController::class, 'update'])
//        ->requirements(['id' => Requirement::DIGITS]);
//    $routes->add('teacher_delete', 'teacher-delete/{id}')->controller([TeacherController::class, 'delete'])
//        ->requirements(['id' => Requirement::DIGITS]);
};
