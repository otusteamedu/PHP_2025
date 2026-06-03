<?php

return [
    [
        'method' => 'GET',
        'path' => '/',
        'controller' => \App\Controller\Http\Web\IndexController::class,
        'action' => 'displayMainPage',
    ],
    [
        'method' => 'GET',
        'path' => '/api/v1/system/health-check',
        'controller' => \App\Controller\Http\Api\SystemHealth\SystemHealthController::class,
        'action' => 'checkSystemHealth',
    ],
    [
        'method' => 'POST',
        'path' => '/api/v1/brackets/balance-check',
        'controller' => \App\Controller\Http\Api\BracketBalance\BracketBalanceController::class,
        'action' => 'checkBracketsBalance',
    ],
    [
        'method' => 'POST',
        'path' => '/api/v1/emails/verify',
        'controller' => \App\Controller\Http\Api\EmailVerification\EmailVerificationController::class,
        'action' => 'verifyEmails',
    ],
    [
        'method' => 'POST',
        'path' => '/api/v1/event',
        'controller' => \App\Controller\Http\Api\EventSystem\EventSystemController::class,
        'action' => 'createEvent',
    ],
    [
        'method' => 'GET',
        'path' => '/api/v1/event',
        'controller' => \App\Controller\Http\Api\EventSystem\EventSystemController::class,
        'action' => 'getEvent',
    ],
    [
        'method' => 'DELETE',
        'path' => '/api/v1/events',
        'controller' => \App\Controller\Http\Api\EventSystem\EventSystemController::class,
        'action' => 'deleteEvents',
    ],
    [
        'method' => 'GET',
        'path' => '/api/v1/user',
        'controller' => \App\Controller\Http\Api\UserManagement\UserManagementController::class,
        'action' => 'getUser',
    ],
    [
        'method' => 'GET',
        'path' => '/api/v1/users',
        'controller' => \App\Controller\Http\Api\UserManagement\UserManagementController::class,
        'action' => 'getUsers',
    ],
    [
        'method' => 'POST',
        'path' => '/api/v1/user',
        'controller' => \App\Controller\Http\Api\UserManagement\UserManagementController::class,
        'action' => 'createUser',
    ],
    [
        'method' => 'PATCH',
        'path' => '/api/v1/user/email',
        'controller' => \App\Controller\Http\Api\UserManagement\UserManagementController::class,
        'action' => 'updateUserEmail',
    ],
    [
        'method' => 'DELETE',
        'path' => '/api/v1/user',
        'controller' => \App\Controller\Http\Api\UserManagement\UserManagementController::class,
        'action' => 'deleteUser',
    ],
];
