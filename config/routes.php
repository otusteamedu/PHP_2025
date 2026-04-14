<?php

return [
    ['GET', '/', \App\Controller\Web\IndexController::class, 'displayMainPage'],
    ['GET', '/infrastructure-health-check', \App\Controller\Web\InfrastructureController::class, 'checkServiceHealth'],
    ['POST', '/parenthesis-verifier', \App\Controller\Web\ParenthesisStringController::class, 'verifyParenthesisString'],
    ['GET', '/session-storage-checker', \App\Controller\Web\SessionController::class, 'checkSessionStorage'],
    ['POST', '/emails-verifier', \App\Controller\Web\EmailController::class, 'verifyEmails'],
    ['POST', '/add-event', \App\Controller\Web\EventController::class, 'addEvent'],
    ['GET', '/get-event', \App\Controller\Web\EventController::class, 'getEvent'],
    ['DELETE', '/delete-events', \App\Controller\Web\EventController::class, 'deleteEvents'],
    ['GET', '/get-user', \App\Controller\Web\User\UserController::class, 'getUser'],
    ['GET', '/get-users', \App\Controller\Web\User\UserController::class, 'getUsers'],
    ['POST', '/create-user', \App\Controller\Web\User\UserController::class, 'createUser'],
    ['PATCH', '/update-user-email', \App\Controller\Web\User\UserController::class, 'updateUserEmail'],
    ['DELETE', '/delete-user', \App\Controller\Web\User\UserController::class, 'deleteUser'],
];
