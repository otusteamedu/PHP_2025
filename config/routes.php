<?php

declare(strict_types=1);

return [
    ['GET', '/', \App\Controller\Http\Web\IndexController::class, 'displayMainPage'],
    ['GET', '/services/health-check', \App\Controller\Http\Api\ServicesHealth\ServicesHealthController::class, 'checkServicesHealth'],
    ['POST', '/brackets/balance-check', \App\Controller\Http\Api\BracketBalance\BracketBalanceController::class, 'checkBracketsBalance'],
    ['POST', '/emails/verify', \App\Controller\Http\Api\Email\EmailController::class, 'verifyEmails'],
    ['POST', '/event/add', \App\Controller\Http\Api\EventSystem\EventSystemController::class, 'addEvent'],
    ['GET', '/event/get', \App\Controller\Http\Api\EventSystem\EventSystemController::class, 'getEvent'],
    ['DELETE', '/events/delete', \App\Controller\Http\Api\EventSystem\EventSystemController::class, 'deleteEvents'],
    ['GET', '/user/get', \App\Controller\Http\Api\User\UserController::class, 'getUser'],
    ['GET', '/users/get', \App\Controller\Http\Api\User\UserController::class, 'getUsers'],
    ['POST', '/user/create', \App\Controller\Http\Api\User\UserController::class, 'createUser'],
    ['PATCH', '/user/update-email', \App\Controller\Http\Api\User\UserController::class, 'updateUserEmail'],
    ['DELETE', '/user/delete', \App\Controller\Http\Api\User\UserController::class, 'deleteUser'],
];
