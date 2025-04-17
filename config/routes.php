<?php

use App\Infrastructure\Http\Teacher\Controller\TeacherController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Requirement\Requirement;

return function (RoutingConfigurator $routes): void {
    $routes->add('teachers_get_all', '/teachers')->controller([TeacherController::class, 'getAll'])->methods(['GET']);
    $routes->add('teacher_get_by_id', '/teachers/{id}')->controller([TeacherController::class, 'getById'])
        ->requirements(['id' => Requirement::DIGITS])
        ->methods(['GET']);

    //TODO use only POST
    $routes->add('teacher_create', 'teacher-create')->controller([TeacherController::class, 'create']);


    $routes->add('teacher_update', 'teacher-update/{id}')->controller([TeacherController::class, 'update'])
        ->requirements(['id' => Requirement::DIGITS]);
    $routes->add('teacher_delete', 'teacher-delete/{id}')->controller([TeacherController::class, 'delete'])
        ->requirements(['id' => Requirement::DIGITS]);
};
